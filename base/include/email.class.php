<?php
use PHPMailer\PHPMailer\PHPMailer;

class Email {
    public $id = 0;
    public $dt = null;
    public $tmp = null;
    public $who = 0;
    public $stat = 0;
    public $dt_open = null;
    public $dat = [];
    public $guid = '';
    public $flags = 0;

	/** @var PHPMailer */
	private $mailer = null;

    const HEADER_MESSAGE_ID   = 'CTRL-DliveryID';
    const HEADER_MESSAGE_GUID = 'CTRL-DliveryGUID';

    const FLAG_TRACK_OPEN   = 0x1;
    const FLAG_TRACK_OPENED = 0x2;

    const STAT_SENT       = 1;
    const STAT_SEND_ERROR = 2;

    public static $debug = '';
    public static $error = '';

    /**
    * Create PHPMailer for send Email messages
    *
    * @param string Имя отправителя
    * @param string Адрес отправителя
    * @param EmailTemplate Шаблон письма
    * @param array Массив данных шаблона
    * @param integer Sender id (mail_who.id) 1-System
    * @param integer Режим отладки (вывод сообщений) 0=Выкл, 1=Клиент, 2=Клиент и Сервер
    * @return Email
    */
	function __construct($senderName = 'Sender Control', $senderAddr = '', $tmp = null, $data = [], $who = 1, $debug = 0) { // test debug = 2
        //date_default_timezone_set('Etc/UTC');
        $this->dt = new DateTime();
        $this->dt_open = new DateTime('2000-01-01');
        $this->tmp = $tmp == null ? EmailTemplate::get() : $tmp;
        $this->dat = $data;
        $this->who = $who;

        if(is_numeric($senderName)) {
            $id = intval($senderName);
            if($id > 0) {
                $row = $DB->select_row("SELECT * FROM mail_log WHERE id = $id");
                if($row) {
                    foreach($row as $k => $v) {
                        switch($k) {
                            case 'dt': $v = new DateTime($v); break;
                            case 'tmp': $v = EmailTemplate::get($v); break;
                            case 'dat': $v = json_decode($v); break;
                            case 'guid': break;
                            default: $v = intval($v); break;
                        }
                        $this->$k = $v;
                    }
                }
            }
        }

        $this->mailer = new PHPMailer;
        $this->mailer->isSMTP();
        $this->mailer->SMTPDebug   = $debug;
        $this->mailer->Timeout     = 5;
        $this->mailer->Debugoutput = function($str, $level) { error_log("SMTP lvl:$level; msg: $str"); }; // 'echo';

        $this->mailer->CharSet     = 'UTF-8';
        $this->mailer->Encoding    = '8bit';
        $this->mailer->Host        = SMTP_HOST;
        $this->mailer->Port        = SMTP_PORT;
        // SMTP Auth
        $this->mailer->SMTPAuth    = true;
        if(SMTP_SECURE) {
            $this->mailer->SMTPSecure  = SMTP_SECURE;
            $this->mailer->SMTPAutoTLS = true;
            //echo "secure " . SMTP_SECURE . "\n";
        } else {
            $this->mailer->SMTPSecure  = false;
            $this->mailer->SMTPAutoTLS = false;
            //echo "unsecure\n";
        }
        /*$this->mailer->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );*/
        $this->mailer->Username    = SMTP_USER;
        $this->mailer->Password    = SMTP_PASS;

        // Sender
        if($senderAddr == '') $senderAddr = SMTP_FROM;
        $this->mailer->setFrom($senderAddr, $senderName);
        $this->mailer->addReplyTo($senderAddr, $senderName);
        if(!$this->id && !$this->tmp->id && empty($this->dat)) {
            $this->dat = [
                'fn' => $senderName,
                'fa' => $senderAddr
            ];
        }
    }

    public function save() {
        $t = new SqlTable('mail_log');
        foreach($this as $k => $v) {
            if($k == 'mailer') continue;
            if($k == 'dat') $v = json_encode($v);
            $t->addFld($k, $v);
        }
        return $t->save($this);
    }

    function setDebugMode($iMode) {
        if($iMode >= 0 && $iMode < 5) {
            $this->mailer->SMTPDebug = $iMode;
        }
    }

    function addCustomHeader($name, $value = null) {
        $this->mailer->addCustomHeader($name, $value);
    }

	function attachFile($fileName) {
		if(file_exists($fileName)) {
			$this->mailer->addAttachment($fileName);
		}
	}

    function bodyFromFile($fileName) {
        if(file_exists($fileName)) {
            $this->mailer->msgHTML(file_get_contents($fileName), dirname(__FILE__));
        }
    }

    public function addRecipient($rcpt, $mail = '') {
        global $DB;
        if(is_array($rcpt)) {
            foreach ($rcpt as $key => $value) {
                if(is_object($value)) {
                    $this->addRecipient($value);
                } elseif(is_numeric($key)) {
                    $this->addRecipient($value);
                } else {
                    $this->addRecipient($key, $value);
                }
            }
            return $this;
        }

        $email = '';
        $name  = '';
        $uid   = 0;
        $pid   = 0;

        if(is_a($rcpt, 'CUser')) {
            $email = $rcpt->email;
            $name  = $rcpt->fi();
            $uid   = $rcpt->id;
        } elseif(is_a($rcpt, 'Person')) {
            $email = $rcpt->email;
            $name  = $rcpt->name;
            $pid   = $rcpt->id;
        } else {
            if($mail != '') {
                $email = $mail;
                $name  = $rcpt;
            } else {
                $email = $rcpt;
                $name  = '';
            }
        }
        $this->mailer->addAddress($email, $name);
        if($this->id) {
            $DB->prepare("INSERT INTO mail_recipients (mail, usr, person, name, email)
                        VALUES (:m, :u, :p, :n, :e)")
                ->bind('m', $this->id)
                ->bind('u', $uid)
                ->bind('p', $pid)
                ->bind('n', $name)
                ->bind('e', $email)
                ->execute();
        }
        return $this;
    }

	public function send($rcpts, $subject = '', $message = '', $isHtml = true, $clearRcpt = true) {
        self::$error = '';

        if($clearRcpt) $this->mailer->clearAllRecipients();

        if($this->id == 0) {
            if($this->tmp->id == 0) {
                $this->dat['s'] = $subject;
                $this->dat['t'] = $message;
            }
            $this->save();
        }

        $this->makeKey();
        $this->addRecipient($rcpts);
        $this->mailer->Subject = $subject;

        if(empty($message)) $message = "empty";

        if($isHtml) {
            $key_file = sprintf(SMTP_TRACK, $this->guid);
            $message .= sprintf('<img src="%s">', $key_file);
            $this->setFlag(self::FLAG_TRACK_OPEN, true);
            $this->mailer->msgHTML($message);
        } else {
            $this->mailer->Body = $message;
            $this->mailer->AltBody = $message;
        }


        if($this->id)   $this->mailer->addCustomHeader(self::HEADER_MESSAGE_ID, $this->id);
        if($this->guid) $this->mailer->addCustomHeader(self::HEADER_MESSAGE_GUID, $this->guid);

        //send the message, check for errors
        $ret = $this->mailer->send();
        self::$error = $this->mailer->ErrorInfo;

        if($this->mailer->SMTPDebug > 0) {
            if($ret) syslog(LOG_ERR, "SMTP message sent");
            else syslog(LOG_ERR, "SMTP mailer error: {$this->mailer->ErrorInfo}");
        }

        $this->stat = $ret ? self::STAT_SENT : self::STAT_SEND_ERROR;
        $this->save();

        return $ret;
    }

    public function makeKey() {
        global $DB;
        if(empty($this->guid) && $this->id) {
            $this->guid = self::createKey($this->tmp->id ? $this->tmp->tname : 'none', $this->id);
            $DB->prepare('UPDATE mail_log SET guid=:g WHERE id=:i')
                ->bind('g', $this->guid)
                ->bind('i', $this->id)
                ->execute();
        }
    }

    public function setFlag($flag, $on = true) {
        if($on) {
            $this->flags |= $flag;
        } else {
            $this->flags &= ~$flag;
        }
    }

    static function createKey($template_name, $id) {
        return md5(sprintf(SMTP_KEY_FMT, date(SMTP_KEY_PAR), $template_name, $id));
    }

    static function trackMail($guid) {
        global $DB;
        $id = intval($DB->prepare("SELECT id FROM mail_log
                                    WHERE flags & :f
                                        AND guid = :g")
                        ->bind('f', self::FLAG_TRACK_OPEN)
                        ->bind('g', $guid)
                        ->execute_scalar());
        if(!$id) return false;
        return $DB->prepare("UPDATE mail_log
                                SET flags = flags | :f,
                                    dt_open = NOW()
                                WHERE id = :i
                                    AND YEAR(dt_open) < 2001")
                ->bind('f', self::FLAG_TRACK_OPENED)
                ->bind('i', $id)
                ->execute();
    }
}
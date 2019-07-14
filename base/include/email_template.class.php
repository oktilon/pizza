<?php
class EmailTemplate {
    public $id = 0;
    public $tname = '';
    public $tfrom = '';
    public $tsubj = '';
    public $tbody = '';

    private static $cache = [];
    public static $total = 0;

    public static $error = '';

    public function __construct($arg = 0) {
        global $DB;
        if(is_numeric($arg) || is_string($arg)) {
            $fld = intval($arg) ? 'id' : 'tname';
            $arg = $DB->prepare("SELECT * FROM mail_templates WHERE $fld = :a")
                    ->bind('a', $arg)
                    ->execute_row();
        }
        if(is_array($arg) || is_object($arg)) {
            foreach($arg as $key => $val) {
                if($key == 'id') $val = intval($val);
                $this->$key = $val;
            }
        }
    }

    public function save() {
        $t = new SqlTable('mail_templates', $this);
        return $t->save($this);
    }

    public function createEmail($who, $data, $debug) {
        $nm = 'Sender Control';
        $em = '';
        if(preg_match('/^(.+)<([\w\d\._-]+@[\w\d\.-]+)>$/', $this->tfrom, $m)) {
            $nm = $m[1];
            $em = $m[2];
        } else {
            $nm = $this->tfrom;
        }
        $m = new Email($nm, $em, $this, $data, $who, $debug);
        $m->save();
        return $m;
    }

    public function replaceVars($text, $data) {
        $ret = $text;
        foreach($data as $k => $v) {
            $parts = mb_split(preg_quote('{{'.$k.'}}'), $ret);
            $ret = implode($v, $parts);
        }
        return $ret;
    }

    public function getBody($data)    { return $this->replaceVars($this->tbody, $data); }
    public function getSubject($data) { return $this->replaceVars($this->tsubj, $data); }

    public function send($user, $data = [], $who = 1, $debug = 0) {
        $mail = $this->createEmail($who, $data, $debug);

        $text = $this->getBody($data);
        $subj = $this->getSubject($data);

        //echo json_encode($text, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;

        $ret = $mail->send($user, $subj, $text, true);
        self::$error = Email::$error;
        return $ret;
    }

    public static function sendTemplate($user, $template, $data = [], $who = 1, $debug = 0) { // 1 - System
        $tmp = EmailTemplate::get($template);
        //echo json_encode($tmp, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
        if(!$tmp->id) return "wrong template $template";
        return $tmp->send($user, $data, $who, $debug);
    }

    public static function get($id = 0) {
        if(!isset(self::$cache[$id])) {
            self::$cache[$id] = new EmailTemplate($id);
        }
        return self::$cache[$id];
    }

    public function getSimple($webix = false) {
        $ret = new stdClass();
        $ret->id = $this->id;
        $fld = $webix ? 'value' : 'tname';
        $ret->$fld = $this->tname;
        return $ret;
    }

    public static function getList($flt = [], $ord = 'tname', $lim = '') {
        global $DB;
        self::$total = 0;
        $ret = [];
        $par = [];
        $add = [];
        foreach($flt as $it) {
            if(is_array($it)) {
                $add[] = $it[0];
                $par[$it[1]] = $it[2];
            } else {
                $add[] = $it;
            }
        }
        $add = $add ? ('WHERE ' . implode(' AND ', $add)) : '';
        $order = $ord ? "ORDER BY $ord" : '';
        $limit = $lim ? "LIMIT $lim" : '';
        $calc  = $lim ? "SQL_CALC_FOUND_ROWS" : '';
        $DB->prepare("SELECT $calc * FROM mail_templates $add $order $limit");
        foreach($par as $k => $v) {
            $DB->bind($k, $v);
        }
        $rows = $DB->execute_all();
        self::$total = count($rows);
        if($calc) {
            self::$total = intval($DB->select_scalar("SELECT FOUND_ROWS()"));
        }
        foreach($rows as $row) {
            $ret[] = new EmailTemplate($row);
        }
        return $ret;
    }
}
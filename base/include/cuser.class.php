<?php
class CUser {
    public $id = 0;
    public $login = 'guest';
    public $password = 'password';
    public $last_name = '';
    public $first_name = '';
    public $flags = 0;
    public $telegram_id = 0;
    public $upd = null;

    public $rights = [];

    private static $cache = [];
    public static $total = 0;
    public static $error = '';

    const LOGIN_ERROR_MESSAGE = 'Помилка логіна або пароля!';
    const PASSWORD_EMPTY      = 'password';

    const FLAG_USER_LOCKED   = 0x0001;

    public function __construct($arg = 0) {
        $this->upd = new DateTime();
        $readAcl = true;
        if(is_numeric($arg)) {
            $id = intval($arg);
            if($id == 0) return;
            $arg = self::getByCondition($id);
        }
        if(is_array($arg) || is_object($arg)) {
            foreach($arg as $key => $val) {
                if($key == 'rights') $readAcl = false;
                $this->$key = self::getProperty($key, $val);
            }
            if($readAcl) $this->readAcl();
        }
    }

    public static function getProperty($k, $v) {
        switch($k) {
            case 'upd': return new DateTime($v);
            case 'telegram_id':
            case 'flags':
            case 'id': return intval($v);
        }
        return $v;
    }

    public function readAcl() {
        global $DB;
        $rows = $DB->prepare("SELECT right_id FROM spr_users_rights WHERE user_id = {$this->id}")
                    ->execute_all();
        foreach($rows as $row) {
            $this->rights[] = intval($row['right_id']);
        }
    }

    public function delete() {
        global $DB;

        $r = $DB->prepare("DELETE FROM spr_users_rights WHERE user_id = :i")
                ->bind('i', $this->id)
                ->execute();
        $u = $DB->prepare("DELETE FROM spr_users WHERE id = :i")
                ->bind('i', $this->id)
                ->execute();
        return $u;
    }

    public function isLocked() {
        return ($this->flags & self::FLAG_USER_LOCKED) > 0;
    }

    private static function getByCondition($value = 0, $field = 'id', $oper = '=') {
        global $DB;
        $DB->prepare("SELECT * FROM spr_users WHERE $field $oper :arg LIMIT 1")
           ->bind('arg', $value);
        return $DB->execute_row();
    }

    public static function byTelegram($tgid) {
        $row = self::getByCondition($tgid, 'telegram_id');
        if(!$row) $row = 0;
        $ret = new CUser($row);
        if($ret->id == 0) $ret->telegram_id = $tgid;
        return $ret;
    }

    public static function byLogin($login) {
        $row = self::getByCondition($login, 'login');
        if(!$row) $row = 0;
        $ret = new CUser($row);
        return $ret;
    }

    public function createSqlTable($json = false) {
        $excl = ['upd'];
        if($json) {
            $excl[] = 'password';
        } else {
            $excl[] = 'rights';
        }
        $t = new SqlTable('spr_users', $this, $excl, 'id', $json);
        return $t;
    }

    public function save() {
        $t = $this->createSqlTable();
        return $t->save($this);
    }

    public function getSessionJson() {
        $t = $this->createSqlTable(true);
        return json_encode($t->fields);
    }

    public function getShortJson() {
        $ret = new stdClass();
        $ret->i = $this->id;
        $ret->l = $this->login;
        $ret->f = $this->fio();
        $ret->e = $this->email;
        $ret->ln = $this->last_name;
        $ret->fn = $this->first_name;
        $ret->mn = $this->middle_name;
        return $ret;
    }

    public function update() {
        foreach($this as $key => $val) {
            if(in_array($key, self::$noDbFields)) continue;
            if(!isset($_POST[$key])) continue;
            if(in_array($key, ['rights'])) {
                $val = [];
                $txt = $_POST[$key];
                if($txt != '') {
                    $arr = explode(',', $txt);
                    foreach($arr as $s) {
                        $val[] = intval($s);
                    }
                }
            } else {
                $val = $_POST[$key];
            }
            $this->$key = self::getProperty($key, $val);
        }
    }

    public function saveTables($tbl, $field, $ids) {
        global $DB;
        $DB->prepare("DELETE FROM $tbl WHERE user_id = :i")
            ->bind('i', $this->id)
            ->execute();

        $ret = true;

        if(count($ids) > 0) {
            $val = [];
            $par = [];
            PageManager::$dbg[] = "saveTables($tbl, $field, " . json_encode($ids) . ")";
            foreach($ids as $i => $v) {
                $val[] = "(:u, :v{$i})";
                $par["v{$i}"] = $v;
            }
            $val = implode(',', $val);
            $DB->prepare("INSERT INTO $tbl (user_id, $field) VALUES $val")
                ->bind('u', $this->id);
            foreach($par as $i => $v) $DB->bind($i, $v);
            $ret = $DB->execute();
        }
        return $ret ? 'ok' : $DB->error;
    }

    public function saveRights()   { return $this->saveTables('spr_users_rights',   'rght_id',    $this->rights); }

    public function getSimple($login = false) {
        $ret = new stdClass();
        $ret->id = $this->id;
        $ret->name = $this->fio();
        $ret->phone = $this->phone;
        $ret->chat = $this->telegram_id;
        if($login) $ret->login = $this->login;
        return $ret;
    }

    public function full() { return $this->fio(false); }

    public function fio($short = true) {
        $l = $this->last_name;
        $f = $this->first_name  ? ($short ? (mb_substr($this->first_name, 0, 1)  . '.') : $this->first_name)  : '';
        if($f)  $l = $l ? "$l $f" : "$f";
        return $l;
    }

    public static function getCurrentUser() {
        $cache = isset($_SESSION['user']) ? json_decode($_SESSION['user']) : 0;
        $u = new CUser($cache);
        return $u;
    }

    public function userHash($pwd) {
        return md5(sprintf(USER_HASH_FMT, $this->login, $pwd));
    }

    public function checkHash($pwd) {
        return $this->password == $this->userHash($pwd);
    }

    public function setPassword($pwd) {
        global $DB;

        $this->password = $this->userHash($pwd);
        $DB->prepare("UPDATE spr_users SET password = :pass WHERE id = :id");
        $DB->bindValue('pass', $this->password);
        $DB->bindValue('id',   $this->id);
        $ret = $DB->execute();
        if($ret) $this->setSession();
        return $ret;
    }

    public function clearPassword() {
        global $DB;

        $this->password = self::PASSWORD_EMPTY;
        $r = $DB->prepare("UPDATE spr_users SET password = :pass WHERE id = :id")
                ->bind('pass', $this->password)
                ->bind('id',   $this->id)
                ->execute();
        return $r;
    }

    public function resetPassword($url) {
        global $PM;
        $pwd = self::generatePassword(8);
        $ok = $this->setPassword($pwd, true);
        syslog(LOG_ERR, "Password reset for {$this->id} = $pwd, [{$DB->error}]");
        if(!$ok) {
            return 'db_error ' . $DB->error;
        }
        $fio  = $this->fi();
        $data = [
            'fio' => $fio,
            'lnk' => $url,
            'pwd' => $pwd
        ];
        $q = EmailTemplate::sendTemplate($this, 'reset-password', $data);
        return $q ? 'ok' : ('error ' . EmailTemplate::$error);
    }

    public function setSession() {
        $_SESSION['user'] = $this->getSessionJson();
    }

    public function hasValidPassword() {
        return $this->password != self::PASSWORD_EMPTY;
    }

    public function hasTemporaryPassword() {
        return (($this->flags & self::FLAG_TMP_PWD) > 0) ||
                $this->password != self::PASSWORD_EMPTY;
    }

    public function hasRights() {
        $ret = true;
        $all = func_get_args();
        if(empty($all)) $all[] = 1;
        foreach ($all as $right) {
            if(!in_array($right, $this->rights)) $ret = false;
        }
        return $ret;
    }

    public static function loginUser($usr, $hash, $pm = null) {
        global $DB;

        $username = $usr;

        if(preg_match('/^(.+)@.+/', $usr, $m)) $username = $m[1];

        $pwd = base64_decode($hash);
        $u = self::verifyLocalUser($username, $pwd);

        if(!$u->id) {
            if($DB->error != '') return $DB->error;
            return self::LOGIN_ERROR_MESSAGE;
        }

        if($u->isLocked()) {
            return 'Доступ заблоковано';
        }

        // $u->setSession();
        // $u->logUserLogin();

        return 'ok';
    }

    public static function verifyLocalUser($username, $password) {
        global $DB;
        $row = $DB->prepare("SELECT * FROM spr_users WHERE login = :login")
                    ->bind('login', $username)
                    ->execute_row();
        if($row) {
            $u = new CUser($row);
            if($u->checkHash($password)) {
                return $u;
            }
        }
        return new CUser(0);
    }

    public static function verifyLdapUser($username, $password) {
        global $DB;
        $DomainName  = LDAP_DOMAIN;
        $ldap_server = LDAP_SERVER;

        $auth_user = $username . '@' . $DomainName;

        $ret  = null;

        if($connect = ldap_connect($ldap_server)){

            if($bind = @ldap_bind($connect, $auth_user, $password)){
                $row = $DB->prepare("SELECT * FROM spr_users WHERE login = :login")
                            ->bind('login', $username)
                            ->execute_row();
                if($row) {
                    $ret = new CUser($row);
                } else {
                    // Create new user from LDAP
                    $ret = new CUser(0);
                    $ret->id = -1;
                    $ret->state = 1;
                }
            } else {
                $err_num = ldap_errno($connect);
                if($err_num != 0x31) {
                    $DB->error = ldap_error($connect);
                }
            }
            ldap_close($connect);
        }

        if($ret == null) $ret = new CUser(0);
        return $ret;
    }

    public static function generatePassword($length = 8) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $len   = strlen($chars);
        return substr(str_shuffle($chars), 0, min($length, $len));
    }

    public static function loginUserApp($usr, $pwd) {
        global $DB;
        self::$error = 'wrong';
        $row = $DB->prepare("SELECT * FROM spr_users WHERE login = :login")
                    ->bind('login', $usr)
                    ->execute_row();
        if(!$row) {
            self::$error = 'bad';
            $row = 0;
        }
        $u = new CUser($row);
        if($u->checkHash($pwd)) {
            self::$error = 'ok';
        }
        if($u->isLocked()) {
            self::$error = 'locked';
        }
        $u->setSession();
        return $u;
    }

    public static function get($id) {
        if(!isset(self::$cache[$id])) {
            self::$cache[$id] = new CUser($id);
        }
        return self::$cache[$id];
    }

    public static function getList($flt = [], $ord = 'id', $lim = '') {
        global $DB;
        self::$total = 0;
        $leaveAllFirms = false;
        $ret = [];
        $par = [];
        $add = [];
        $obj = true;
        $emp = true;
        $fld = '';
        $flds = '*';
        foreach($flt as $it) {
            if($it == 'leaveAllFirms') {
                $leaveAllFirms = true;
            } elseif($it == 'no_empty') {
                $emp = false;
            } elseif($it == 'id_only') {
                $flds = $fld = 'id';
                $obj = false;
            } elseif(is_array($it)) {
                $cond = array_shift($it);
                //PageManager::debug($cond, 'cond');
                if($cond == 'right' && $it) {
                    $it = $it[0];
                    //PageManager::debug($it, 'it1');
                    if(!is_numeric($it)) { $it = CRight::findByText($it); }
                    else { $it = intval($it); }
                    //PageManager::debug($it, 'it2');
                    $q = $DB->prepare("SELECT GROUP_CONCAT(DISTINCT user_id ORDER BY user_id)
                                FROM spr_users_rights
                                WHERE rght_id IN($it)")
                        ->execute_scalar();
                    //PageManager::debug($q, 'q');
                    $add[] = $q ? "id IN($q)" : "id IN(-1)";
                } else {
                    if(count($it) > 1) {
                        if($cond) $add[] = $cond;
                        $par[$it[0]] = $it[1];
                    }
                }
            } else {
                $add[] = $it;
            }
        }
        $add = $add ? ('WHERE ' . implode(' AND ', $add)) : '';
        $order = $ord ? "ORDER BY $ord" : '';
        $limit = $lim ? "LIMIT $lim" : '';
        $calc  = $lim ? "SQL_CALC_FOUND_ROWS" : '';
        $DB->prepare("SELECT $calc $flds FROM spr_users $add $order $limit");
        foreach($par as $k => $v) {
            $DB->bind($k, $v);
        }
        $rows = $DB->execute_all();
        //PageManager::$dbg[] = "err={$DB->error}";
        self::$total = count($rows);
        if($calc) {
            self::$total = intval($DB->select_scalar("SELECT FOUND_ROWS()"));
        }
        foreach($rows as $row) {
            $ret[] = $obj ? new CUser($row, $leaveAllFirms) : ($fld ? intval($row[$fld]) : $row);
        }
        if(!$ret && !$obj && $fld && !$emp) $ret[] = -1;
        return $ret;
    }
}



<?php
class CUser {
    public $id = 0;
    public $login = 'guest';
    public $password = 'password';
    public $inn = '';
    public $last_name = '';
    public $first_name = '';
    public $middle_name = '';
    public $email = '';
    public $phone = '';
    public $gphone = '';
    public $date_create = null;
    public $last_logon_time = null;
    public $state = 0;
    public $kod1c = '';
    public $author = 0;
    public $address = '';
    public $lette_reg = 0;
    public $flags = 0;
    public $theme = '';
    public $snd_lang = '';
    public $telegram_id = 0;
    public $upd = null;

    public $firms = [];
    public $all_firms = false;
    public $clusters = [];
    public $rights = [];

    private static $cache = [];
    public static $total = 0;
    public static $error = '';

    private static $noDbFields = ['upd'];
    private static $jsonFields = ['firms', 'all_firms', 'rights', 'clusters'];

    const LOGIN_ERROR_MESSAGE = 'Помилка логіна або пароля!';
    const PASSWORD_EMPTY      = 'password';

    const FLAG_FINANCIAL_RESPONSIBLE_USER   = 0x0001;
    const FLAG_TMP_PWD                      = 0x0002; // Password is temporary

    public function __construct($arg = 0, $leaveAllFirms = false) {
        $this->date_create = new DateTime();
        $this->last_logon_time = new DateTime();
        $this->upd = new DateTime();
        $readAcl = true;
        if(is_numeric($arg)) {
            $id = intval($arg);
            if($id == 0) return;
            $arg = self::getByCondition($id);
        }
        if(is_array($arg) || is_object($arg)) {
            foreach($arg as $key => $val) {
                if($key == 'firms') $readAcl = false;
                $this->$key = self::getProperty($key, $val);
            }
            if($readAcl) $this->readAcl($leaveAllFirms);
        }
    }

    public static function getProperty($k, $v) {
        switch($k) {
            case 'date_create':
            case 'last_logon_time':
            case 'upd': return new DateTime($v);
            case 'telegram_id':
            case 'lette_reg':
            case 'author':
            case 'flags':
            case 'state':
            case 'id': return intval($v);
        }
        return $v;
    }

    public function readAcl($leaveAllFirms = false) {
        global $DB;
        $rows = $DB->select("SELECT rght_id FROM dn_users_rights WHERE user_id = {$this->id}");
        foreach($rows as $row) {
            $this->rights[] = intval($row['rght_id']);
        }

        $rows = $DB->select("SELECT cluster_id FROM dn_users_clusters WHERE user_id = {$this->id}");
        foreach($rows as $row) {
            $this->clusters[] = intval($row['cluster_id']);
        }

        $rows = $DB->select("SELECT firm_id FROM dn_users_firms WHERE user_id = {$this->id}");
        foreach($rows as $row) {
            $id = intval($row['firm_id']);
            if($id == 0) {
                $this->all_firms = true;
                if($leaveAllFirms) $this->firms[] = $id;
            } else {
                $this->firms[] = $id;
            }
        }
    }

    public function delete() {
        global $DB;

        $r = $DB->prepare("DELETE FROM dn_users_rights WHERE user_id = :i")
                ->bind('i', $this->id)
                ->execute();
        $f = $DB->prepare("DELETE FROM dn_users_firms WHERE user_id = :i")
                ->bind('i', $this->id)
                ->execute();
        $c = $DB->prepare("DELETE FROM dn_users_clusters WHERE user_id = :i")
                ->bind('i', $this->id)
                ->execute();
        $u = $DB->prepare("DELETE FROM dn_users WHERE id = :i")
                ->bind('i', $this->id)
                ->execute();
        return $u;
    }

    public function isLocked() {
        return $this->state == 1;
    }

    private static function getByCondition($value = 0, $field = 'id', $oper = '=') {
        global $DB;
        $DB->prepare("SELECT * FROM dn_users WHERE $field $oper :arg LIMIT 1")
           ->bind('arg', $value);
        return $DB->execute_row();
    }

    public static function findByText($txt, $limit = 0, $implode = false) {
        $flt = [];
        $arr = explode(' ', $txt);
        $cap = ['last_name', 'first_name', 'middle_name'];
        $cnt = count($arr);
        if($cnt == 1) {
            $cap = ["CONCAT(last_name, first_name, middle_name)"];
        }
        foreach($arr as $i=>$t) {
            $fld = $i < 3 ? $cap[$i] : $cap[0];
            $flt[] =  ["$fld LIKE :n$i", "n$i", "%$t%"];
        }
        $ord = $implode ? 'id' : 'last_name';
        if($implode) $flt[] = 'id_only';
        //array_merge(PageManager::$dbg, $flt);
        $ret = self::getList($flt, $ord, $limit);
        if($implode) {
            $ret = implode(',', $ret);
        }
        return $ret;
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

    public static function byRight($rights) {
        global $DB;
        $ret = [];
        $lst = is_array($rights) ? implode(',', $rights) : $rights;
        $ids = $DB->prepare("SELECT user_id
                        FROM dn_users_rights
                        WHERE rght_id IN($lst)")
                ->execute_all();
        foreach($ids as $idr) {
            $ret[] = new CUser($idr['user_id']);
        }
        return $ret;
    }


    public static function getUsersUploadFolder($root = true) {
        return ($root ? PATH_ROOT : '') . DIRECTORY_SEPARATOR .
            'images' . DIRECTORY_SEPARATOR .
            'upload' . DIRECTORY_SEPARATOR;
    }

    public function getUserUploadFolder($file = '', $root = true) {
        return self::getUsersUploadFolder($root) .
            $this->id . DIRECTORY_SEPARATOR . $file;
    }

    public function initUploadFolder() {
        $upl  = self::getUsersUploadFolder();
        $path = $this->getUserUploadFolder();
        if(is_dir($upl) || is_writable($upl)) {
            if(!is_dir($path)) {
                mkdir($path);
            }
            if(is_dir($path) && is_writeable($path)) {
                return '';
            }
            return 'User folder ' . (is_dir($path) ? 'error' : 'absent');
        }
        return 'Upload folder ' . (is_dir($upl) ? 'error' : 'absent')  ;
    }

    public function getTelegramUserLogin() {
        global $DB, $PM;

        if($this->telegram_id == 0) {
            $PM->set('err', 'invalid_id');
            return '';
        }
        $login = $DB->prepare('SELECT login FROM dn_users_new WHERE id = :tgi')
                    ->bind('tgi', $this->telegram_id)
                    ->execute_scalar();

        if($login === false) {
            $q = $DB->prepare('INSERT INTO dn_users_new (id) VALUES (:tgi)')
                    ->bind('tgi', $this->telegram_id)
                    ->execute();
            if(!$q) {
                $PM->set('err', $DB->error);
                return '';
            }
            $login = '';
        }
        return $login;
    }

    public function updateTelegramUser($tgid, $pass) {
        global $DB;

        if($tgid == 0) return 'Invalid telegram_id';
        if($pass == '1') $pass = '';

        $valid = $pass ? $this->checkHash($pass) : false;

        $ok = $DB->prepare("INSERT INTO dn_users_new (id, login)
                            VALUES (:id, :lg)
                            ON DUPLICATE KEY UPDATE
                            login = :lg")
                ->bind('lg', $this->login)
                ->bind('id', $tgid)
                ->execute();

        if($valid) {
            $this->telegram_id = $tgid;
            $ok = $DB->prepare("UPDATE dn_users SET telegram_id = :tg WHERE id = :id")
                    ->bind('tg', $tgid)
                    ->bind('id', $this->id)
                    ->execute();
            if($ok) {
                $DB->prepare('DELETE FROM dn_users_new WHERE id = :id')
                    ->bind('id', $tgid)
                    ->execute();
            }
            return $ok ? 'well' : $DB->error;
        }
        return $pass ? 'Невірний пароль' : ($ok ? 'ok' : $DB->error);
    }

    public function createSqlTable($json = false) {
        $t = new SqlTable('dn_users', null, [], 'id', $json);
        foreach($this as $key => $val) {
            if(in_array($key, self::$noDbFields)) continue;
            if(!$json && in_array($key, self::$jsonFields)) continue;
            if($json && $key == 'password') continue;
            $t->addFld($key, $val);
        }
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
            if(in_array($key, ['firms', 'rights', 'clusters'])) {
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

    public function saveRights()   { return $this->saveTables('dn_users_rights',   'rght_id',    $this->rights); }
    public function saveFirms()    { return $this->saveTables('dn_users_firms',    'firm_id',    $this->firms); }
    public function saveClusters() { return $this->saveTables('dn_users_clusters', 'cluster_id', $this->clusters); }

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
        $m = $this->middle_name ? ($short ? (mb_substr($this->middle_name, 0, 1) . '.') : $this->middle_name) : '';
        if($f)  $l = $l ? "$l $f" : "$f";
        if($m)  $l = $l ? "$l $m" : "$m";
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
        $DB->prepare("UPDATE dn_users SET password = :pass WHERE id = :id");
        $DB->bindValue('pass', $this->password);
        $DB->bindValue('id',   $this->id);
        $ret = $DB->execute();
        if($ret) $this->setSession();
        return $ret;
    }

    public function clearPassword() {
        global $DB;

        $this->password = self::PASSWORD_EMPTY;
        $r = $DB->prepare("UPDATE dn_users SET password = :pass WHERE id = :id")
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

    public function logUserLogin() {
        global $DB, $PM;
        $q = $DB->prepare("INSERT INTO dn_users_log
                        (user, user_login, ip, ua) VALUES (:id, :ul, :ip, :ua)")
                ->bind('id', $this->id)
                ->bind('ul', $this->login)
                ->bind('ip', $PM->remoteIp)
                ->bind('ua', $PM->userAgent)
                ->execute();
        return $q;
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

    public static function loginUser($usr, $pwd, $pm = null) {
        global $DB;

        $username = $usr;

        if(preg_match('/^(.+)@.+/', $usr, $m)) $username = $m[1];

        $u = self::verifyLocalUser($username, $pwd);

        if(!$u->id) {
            if($DB->error != '') return $DB->error;
            return self::LOGIN_ERROR_MESSAGE;
        }

        if($u->isLocked()) {
            return 'Доступ заблоковано';
        }

        $u->setSession();
        $u->logUserLogin();

        return 'ok';
    }

    public static function verifyLocalUser($username, $password) {
        global $DB;
        $row = $DB->prepare("SELECT * FROM dn_users WHERE login = :login")
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
                $row = $DB->prepare("SELECT * FROM dn_users WHERE login = :login")
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
        $row = $DB->prepare("SELECT * FROM dn_users WHERE login = :login")
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
                                FROM dn_users_rights
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
        $DB->prepare("SELECT $calc $flds FROM dn_users $add $order $limit");
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



<?php
class Page {
    public $id = 0;
    public $title = 'unknown';
    public $folder = 'view';
    public $script = '';
    public $descr = 'GPS Corezoid';
    public $flags = 0;
    public $author = 'GPS Corezoid';

    public $acl = [];

    private static $cache = [];
    public static $total = 0;
    public static $error = '';
    public static $debug = '';

    public function __construct($arg = 0) {
        global $DB;
        if(is_numeric($arg)) {
            $id = intval($arg);
            if($id == 0) return;
            $arg = $DB->select_row("SELECT * FROM pages WHERE id = $id");
        }
        if(is_array($arg) || is_object($arg)) {
            foreach($arg as $key => $val) {
                $this->$key = $this->getProperty($key, $val);
            }
        }
        $this->readAcl();
    }

    private function getProperty($k, $v) {
        switch ($k) {
            case 'flags':
            case 'id': return intval($v);
        }
        return $v;
    }

    public function save() {
        $t = new SqlTable('pages', $this, ['acl']);
        $r = $t->save($this);
        if($r) {
            self::$error = $this->saveAcl() ? 'ok' : $DB->error;
        }
        return $r;
    }

    public function saveAcl() {
        global $DB;
        if($this->id == 0) return false;
        $DB->prepare('DELETE FROM pages_acl WHERE page_id = :i')
            ->bind('i', $this->id)
            ->execute();
        $val = [];
        $par = [];
        $cnt = 1;
        foreach($this->acl as $r_id => $lvl) {
            $r = "r$cnt";
            $l = "l$cnt";
            $val[] = "(:m, :$r, :$l)";
            $par[$r] = $r_id;
            $par[$l] = $lvl;
            $cnt++;
        }
        if($val) {
            $val = implode(',', $val);
            $DB->prepare("INSERT INTO pages_acl VALUES $val")
                ->bind('m', $this->id);
            foreach($par as $k => $v) $DB->bind($k, $v);
            return $DB->execute();
        }
        return true;
    }


    public function readAcl() {
        global $DB;
        $this->acl = [];
        if(!$DB->valid() || $this->id == 0) return;
        $q = $DB->prepare("SELECT right_id, level FROM pages_acl WHERE page_id = :i")
                ->bind('i', $this->id)
                ->execute_all();
        foreach($q as $r) {
            $this->acl[intval($r['right_id'])] = intval($r['level']);
        }
    }

    public function delete() {
        global $DB;
        $DB->prepare("DELETE FROM pages_acl WHERE page_id = :i")
            ->bind('i', $this->id)
            ->execute();
        return $DB->prepare("DELETE FROM pages WHERE id = :i")
                ->bind('i', $this->id)
                ->execute();
    }

    public function update() {
        foreach($this as $key => $val) {
            if($key == 'id') continue;
            if(!isset($_POST[$key])) continue;
            if(in_array($key, ['acl'])) {
                $val = [];
                $txt = $_POST[$key];
                if($txt != '') {
                    $arr = explode(',', $txt);
                    foreach($arr as $s) {
                        $kvp = explode(':', $s);
                        $k = intval($kvp[0]);
                        $v = isset($kvp[1]) ? intval($kvp[1]) : 0;
                        $val[$k] = $v;
                    }
                }
            } else {
                $val = $_POST[$key];
            }
            $this->$key = self::getProperty($key, $val);
        }
    }

    public function getSimple() {
        $ret = new stdClass();
        $arr = ['id', 'title'];
        foreach($arr as $k) {
            $ret->$k = $this->$k;
        }
        return $ret;
    }

    public function getJson() {
        $ret = new stdClass();
        foreach($this as $k=>$v) {
            if(is_object($v)) $v = $v->getSimple();
            $ret->$k = $v;
        }
        return $ret;
    }

    public static function get($id) {
        if(!isset(self::$cache[$id])) {
            self::$cache[$id] = new Page($id);
        }
        return self::$cache[$id];
    }

    public static function findByText($txt, $limit = 0, $implode = false) {
        $flt = [
            ['title LIKE :n', 'n', "%$txt%"]
        ];

        $ord = $implode ? 'id' : 'title';
        if($implode) $flt[] = 'id_only';
        $ret = self::getList($flt, $ord, $limit);
        if($implode) {
            $ret = implode(',', $ret);
        }
        return $ret;
    }

    public static function getList($flt = [], $ord = 'title', $lim = '') {
        global $DB;
        self::$total = 0;
        $glue = ' AND ';
        $obj = true;
        $fld = '';
        $flds = '*';
        $ret = [];
        $par = [];
        $add = [];
        foreach($flt as $it) {
            if($it == 'or') {
                $glue = ' OR ';
            } elseif($it == 'id_only') {
                $flds = $fld = 'id';
                $obj  = false;
            } elseif(is_array($it)) {
                $cond = array_shift($it);
                if($cond) $add[] = $cond;
                $par[$it[0]] = $it[1];
            } else {
                $add[] = $it;
            }
        }
        $add = $add ? ('WHERE ' . implode($glue, $add)) : '';
        $order = $ord ? "ORDER BY $ord" : '';
        $limit = $lim ? "LIMIT $lim" : '';
        $calc  = $lim ? "SQL_CALC_FOUND_ROWS" : '';
        $DB->prepare("SELECT $calc $flds FROM pages $add $order $limit");
        foreach($par as $k => $v) {
            $DB->bind($k, $v);
        }

        $rows = $DB->execute_all();
        self::$error = $DB->error;
        self::$debug = $DB->sql;
        $total = $calc ? intval($DB->select_scalar("SELECT FOUND_ROWS()")) : count($rows);
        foreach($rows as $row) {
            $ret[] = $obj ? new Page($row) : ($fld ? intval($row[$fld]) : $row);
        }
        self::$total = $total;
        return $ret;
    }
}
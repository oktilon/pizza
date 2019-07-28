<?php
class Menu {
    public $id = 0;
    public $kind = '';
    public $ord = 0;
    public $name = '';
    public $pic = '';
    public $desc = '';
    public $flags = 0;

    public $content = [];
    public $prices = [];
    public $aux = [];

    public static $total = 0;
    public static $error = '';

    public const FLAG_MENU_DELETED = 0x01;

    public function __construct($arg = 0) {
        global $DB;
        if(is_numeric($arg)) {
            $id = intval($arg);
            if($id == 0) return;
            $arg = $DB->select_row("SELECT * FROM menu WHERE id = $id");
        }
        if(is_array($arg) || is_object($arg)) {
            foreach($arg as $key => $val) {
                $this->$key = $this->getProperty($key, $val);
            }
        }
        $this->readContent();
        $this->readPrices();
        $this->readAuxItems();
    }

    private function getProperty($k, $v) {
        switch ($k) {
            case 'flags':
            case 'ord':
            case 'id': return intval($v);
        }
        return $v;
    }

    public function readContent() {
        $this->content = Ingridient::getList([['for', $this->id]]);
    }

    public function readPrices() {
        $this->prices = Price::getList([['prod = :p', 'p', $this->id]]);
    }

    public function readAuxItems() {
        $this->aux = Ingridient::getList([['aux', $this->id]]);
    }

    public function save() {
        $t = new SqlTable('menu', $this, ['content', 'prices', 'aux']);
        $r = $t->save($this);
        if($r) {
            // save items
        }
        return $r;
    }

    public function delete() {
        global $DB;
        $DB->prepare("UPDATE menu SET flags = flags | :f WHERE id = :i")
            ->bind('f', self::FLAG_MENU_DELETED)
            ->bind('i', $this->id)
            ->execute();
    }

    public function update() {
        //
    }

    public function getSimple() { return $this->getJson(); }

    public function getJson() {
        $ret = new stdClass();
        foreach($this as $k=>$v) {
            $ret->$k = $v;
        }
        return $ret;
    }

    public static function getList($flt = [], $ord = 'ord', $lim = '') {
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
        $DB->prepare("SELECT $calc $flds FROM menu $add $order $limit");
        foreach($par as $k => $v) {
            $DB->bind($k, $v);
        }

        $rows = $DB->execute_all();
        self::$error = $DB->error;
        $total = $calc ? intval($DB->select_scalar("SELECT FOUND_ROWS()")) : count($rows);
        foreach($rows as $row) {
            $ret[] = $obj ? new Menu($row) : ($fld ? intval($row[$fld]) : $row);
        }
        self::$total = $total;
        return $ret;
    }
}
<?php
class Ingridient {
    public $id = 0;
    public $name = '';
    public $pic = '';
    public $flags = 0;
    public $price = 0;

    public static $total = 0;
    public static $error = '';

    public const FLAG_INGRIDIENT_DELETED = 0x01;

    public function __construct($arg = 0) {
        global $DB;
        if(is_numeric($arg)) {
            $id = intval($arg);
            if($id == 0) return;
            $arg = $DB->select_row("SELECT * FROM ingridients WHERE id = $id");
        }
        if(is_array($arg) || is_object($arg)) {
            foreach($arg as $key => $val) {
                $this->$key = $this->getProperty($key, $val);
            }
        }
    }

    private function getProperty($k, $v) {
        switch ($k) {
            case 'name':
            case 'pic': return $v;
        }
        return intval($v);
    }

    public function save() {
        $t = new SqlTable('ingridients', $this, ['price']);
        return $t->save($this);
    }

    public function delete() {
        global $DB;
        $DB->prepare("UPDATE ingridients SET flags = flags | :f WHERE id = :i")
            ->bind('f', self::FLAG_INGRIDIENT_DELETED)
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

    public static function getList($flt = [], $ord = 'name', $lim = '') {
        global $DB;
        self::$total = 0;
        $glue = ' AND ';
        $obj = true;
        $prod = 0;
        $fld = '';
        $flds = 'i.*';
        $tbl = '';
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
                if($cond == 'for') {
                    $prod = intval(array_shift($it));
                    if($prod) {
                        $par['pr'] = $prod;
                        $add[] = 'prod = :pr';
                        $ord = $ord ? "ord, $ord" : 'ord';
                        $tbl = 'receipts';
                    }
                } elseif($cond == 'aux') {
                    $prod = intval(array_shift($it));
                    if($prod) {
                        $par['pr'] = $prod;
                        $add[] = 'prod = :pr';
                        $flds .= ', price';
                        $tbl = 'aux_ingridients';
                    }
                } else {
                    if($cond) $add[] = $cond;
                    $par[$it[0]] = $it[1];
                }
            } else {
                $add[] = $it;
            }
        }
        $add = $add ? ('WHERE ' . implode($glue, $add)) : '';
        $order = $ord ? "ORDER BY $ord" : '';
        $limit = $lim ? "LIMIT $lim" : '';
        $calc  = $lim ? "SQL_CALC_FOUND_ROWS" : '';
        $join = $tbl ? "LEFT JOIN $tbl ON ingr = i.id" : '';
        $DB->prepare("SELECT $calc $flds FROM ingridients i $join $add $order $limit");
        foreach($par as $k => $v) {
            $DB->bind($k, $v);
        }

        $rows = $DB->execute_all();
        self::$error = $DB->error;
        $total = $calc ? intval($DB->select_scalar("SELECT FOUND_ROWS()")) : count($rows);
        foreach($rows as $row) {
            $ret[] = $obj ? new Ingridient($row) : ($fld ? intval($row[$fld]) : $row);
        }
        self::$total = $total;
        return $ret;
    }
}
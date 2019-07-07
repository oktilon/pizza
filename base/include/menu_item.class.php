<?php
class MenuItem {
    // Item format
    // sprintf ARGS:
    // 1=HREF,
    // 2=TEXT,
    // 3=TARGET
    const ITEM_FMT =
        '        <li class="nav-item%4$s">' . PHP_EOL .
        '          <a class="nav-link" href="%1$s"%3$s>%2$s</a>' . PHP_EOL .
        '        </li>' . PHP_EOL;

    // Dropdown item format
    // sprintf ARGS:
    // 1=HREF,
    // 2=TEXT,
    // 3=TARGET
    const DROP_ITEM_FMT =
        '            <a class="dropdown-item%4$s" href="%1$s"%3$s>%2$s</a>' . PHP_EOL;

    // Dropdown item format
    // sprintf ARGS:
    // 1=DROP_HREF,
    // 2=DROP_TEXT,
    // 3=DROP_ID,
    // 4=DROP_TARGET
    // 5=DROP_ITEMS
    const DROP_FMT =
        '        <li class="nav-item dropdown%6$s">' . PHP_EOL .
        '          <a class="nav-link dropdown-toggle" '.
                        'href="%1$s" id="%3$s" data-toggle="dropdown" '.
                        'aria-haspopup="true" aria-expanded="false"%4$s>' . PHP_EOL .
        '            %2$s' . PHP_EOL .
        '          </a>' . PHP_EOL .
        '          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="%3$s">' . PHP_EOL .
                        '%5$s' .
        '          </div>' . PHP_EOL .
        '        </li>' . PHP_EOL;

    const SEPARATOR = '<hr style="margin:2px;">' . PHP_EOL;

    const LOGOUT_FORM =
        '      <form class="form-inline my-0">' . PHP_EOL .
        '        <img id="g_loader" class="d-none" src="/images/loader2.gif" width="40" height="40"/>' . PHP_EOL .
        '        <a href="/logout" class="btn btn-outline-secondary my-2 my-sm-0" >Вийти</a>' . PHP_EOL .
        '      </form>' . PHP_EOL;

    const FLAG_TARGET_BLANK   = 0x01;
    const FLAG_ACTIVE         = 0x02;
    const FLAG_NO_ADMIN       = 0x04;
    const FLAG_AND_RIGHTS     = 0x08;

    const FLAG_NO_RENDER      = 0x10;
    const FLAG_IS_INFO        = 0x20;
    const FLAG_IS_DISABLED    = 0x40;
    const FLAG_IS_SEPARATOR   = 0x80;

    public $id = 0;
    public $parent = 0;
    public $page_id = 0;
    public $href = '';
    public $text = '';
    public $pos = 0;
    public $flags = 0;
    public $rights = [];

    public $children = [];

    private static $cache = [];
    public static $total = 0;
    public static $error = '';

    private static $noDbFields = ['children'];

    public static $flags_nm = [
        ['f' => self::FLAG_TARGET_BLANK,'n' => 'У новому вікні',    'i' => 'fa fa-external-link text-primary'],
        ['f' => self::FLAG_ACTIVE,      'n' => 'Активна',           'i' => 'fa fa-circle text-success'],
        ['f' => self::FLAG_NO_ADMIN,    'n' => 'Лише права',        'i' => 'fa fa-briefcase text-danger'],
        ['f' => self::FLAG_AND_RIGHTS,  'n' => 'Усі права',         'i' => 'fa fa-plus-circle text-info'],
        ['f' => self::FLAG_NO_RENDER,   'n' => 'Без рендеру',       'i' => 'fa fa-window-close text-info'],
        ['f' => self::FLAG_IS_INFO,     'n' => 'Інформаційний',     'i' => 'fa fa-info-circle text-warning'],
        ['f' => self::FLAG_IS_DISABLED, 'n' => 'Відключений',       'i' => 'fa fa-toggle-off text-secondary'],
        ['f' => self::FLAG_IS_SEPARATOR,'n' => 'Роздільник',        'i' => 'fa fa-ellipsis-h text-secondary']
    ];

    public function __construct($arg = '', $text = '', $blank = false, $parent = 0) {
        global $DB, $PM;
        if(is_numeric($arg)) {
            if(!$DB->valid()) {
                $arg = json_decode(MENU_NO_DB);
            } else {
                $arg = $arg > 0 ? $DB->select_row("SELECT * FROM menu WHERE id = $arg") : '';
            }
        }
        if(is_array($arg) || is_object($arg)) {
            foreach($arg as $k => $v) $this->$k = self::getProperty($k, $v);
            if($text !== FALSE) {
                $this->getChildren();
            }
        } else {
            $this->href = $arg;
            $this->text = $text;
            $this->parent = $parent;
            $this->setFlag(self::FLAG_TARGET_BLANK, $blank);
        }
        $active = false;
        if(preg_match('/^\/(\w+)(\/(\w+))*(\/.+)*$/', $this->href, $m)) {
            //PageManager::$dbg[] = $this->href . '=' . implode('|', $m);
            if(isset($m[3]) && $m[3]) {
                $active = $PM->folder == $m[1] &&
                          $PM->script == $m[3];
            } else {
                $active = $PM->folder == $m[1];
            }
            if(!$active && $PM->folder == 'view') {
                $active = $PM->script == $m[1];
            }
        }
        $this->setFlag(self::FLAG_ACTIVE, $active);
        $this->readAcl();
    }

    public static function getProperty($k, $v) {
        switch($k) {
            case 'rights':
            case 'href':
            case 'text': return $v;
        }
        return intval($v);
    }

    public function readAcl() {
        global $DB;
        if($DB->valid() && $this->id) {
            $l = $DB->prepare('SELECT rght_id FROM menu_access WHERE menu_id = :i ORDER BY rght_id')
                    ->bind('i', $this->id)
                    ->execute_all();
            foreach ($l as $r) {
                $this->rights[] = intval($r['rght_id']);
            }
        }
    }

    public function testFlag($flag) {
        return ($this->flags & $flag) > 0;
    }

    public function setFlag($flag, $boolValue) {
        $this->flags = $boolValue ? ($this->flags | $flag) : ($this->flags & (~$flag));
    }

    public function isTargetBlank() { return $this->testFlag(self::FLAG_TARGET_BLANK); }
    public function isActive() { return $this->testFlag(self::FLAG_ACTIVE); }
    public function isInfo() { return $this->testFlag(self::FLAG_IS_INFO); }
    public function isDisabled() { return $this->testFlag(self::FLAG_IS_DISABLED); }
    public function noAdmin() { return $this->testFlag(self::FLAG_NO_ADMIN); }
    public function noRender() { return $this->testFlag(self::FLAG_NO_RENDER); }
    public function andRights() { return $this->testFlag(self::FLAG_AND_RIGHTS); }
    public function isSeparator() { return $this->testFlag(self::FLAG_IS_SEPARATOR); }

    public function getChildren() {
        if($this->id == 0) return;
        $this->children = self::loadMenu($this->id);
    }

    public function hasChildren() {
        return count($this->children) > 0;
    }

    public function delete() {
        global $DB;

        $u = $DB->prepare("DELETE FROM menu WHERE id = :i")
                ->bind('i', $this->id)
                ->execute();
        return $u;
    }

    public function save() {
        global $DB;
        $t = new SqlTable('menu', $this, ['rights', 'children']);
        $r = $t->save($this);
        if($r) {
            self::$error = $this->saveRights() ? 'ok' : $DB->error;
        }
        return $r;
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

    public function saveRights() {
        global $DB;
        if($this->id == 0) return false;
        $DB->prepare('DELETE FROM menu_access WHERE menu_id = :i')
            ->bind('i', $this->id)
            ->execute();
        $val = [];
        $par = [];
        foreach($this->rights as $i => $id) {
            $r = "r$i";
            $val[] = "(:m, :$r)";
            $par[$r] = $id;
        }
        if($val) {
            $val = implode(',', $val);
            $DB->prepare("INSERT INTO menu_access VALUES $val")
                ->bind('m', $this->id);
            foreach($par as $k => $v) $DB->bind($k, $v);
            return $DB->execute();
        }
        return true;
    }

    public static function get($id) {
        if(!isset(self::$cache[$id])) {
            self::$cache[$id] = new MenuItem($id);
        }
        return self::$cache[$id];
    }

    public function myParent() {
        global $DB;
        $id = $this->parent;
        if($id) $this->parent = $DB->select_scalar("SELECT text FROM menu WHERE id=$id");
        else $this->parent = '';
    }

    public function readPage() {
        $p = Page::get($this->page_id);
        if($p->id == 0) $p->title = '';
        $this->page = $p;
    }

    public function geti18nText() {
        return preg_match('/^\#(.*)$/', $this->text, $m) ? _($m[1]) : $this->text;
    }

    public function render(CUser $user = null) {
        $html = '';
        if($this->noRender()) return $html;
        if(!$user || $this->hasAccess($user)) {
            if($this->isSeparator()) return self::SEPARATOR;
            $href = $this->href ? $this->href : '#';
            $text = $this->geti18nText();
            $target = $this->isTargetBlank() ? ' target="_blank"' : '';
            $active = $this->isActive() ? ' active' : '';
            $disabled = $this->isDisabled() ? ' disabled' : '';
            if($this->isDisabled()) $href = '#';

            if($this->hasChildren()) {
                $sub = '';
                foreach ($this->children as $child) {
                    $sub .= $child->render($user);
                }
                $id = "navbarDropdown" . $this->id;
                $html = sprintf(self::DROP_FMT, $href, $text, $id, $target, $sub, $active);
            } elseif($this->parent > 0) {
                $html = sprintf(self::DROP_ITEM_FMT, $href, $text, $target, $disabled);
            } else {
                $html = sprintf(self::ITEM_FMT, $href, $text, $target, $active);
            }
        }
        return $html;
    }

    public function renderSelfOnly() {
        $html = '';
        $href = $this->href ? $this->href : '#';
        $text = $this->geti18nText();
        $target = $this->testFlag(self::FLAG_TARGET_BLANK) ? ' target="_blank"' : '';
        $active = $this->isActive() ? ' active' : '';

        $html = sprintf(self::ITEM_FMT, $href, $text, $target, $active);
        return $html;
    }

    public function hasAccess(CUser $u) {
        if(!$this->rights) return true;
        if(!$this->noAdmin() && $u->hasRights()) return true;
        $and = $this->andRights();
        $ok  = $and;
        foreach ($this->rights as $id) {
            $has = $u->hasRights($id);
            if($and && !$has) $ok = false;
            if(!$and && $has) $ok = true;
        }
        return $ok;
    }

    public static function loadMenu($id) {
        global $DB;
        $menu = [];
        if($DB->valid()) {
            $rows = $DB->select("SELECT * FROM menu WHERE parent = $id ORDER BY pos");
            foreach($rows as $row) {
                $mi = new MenuItem($row);
                $menu[] = $mi;
            }
        }
        return $menu;
    }

    public static function getMenu($id, CUser $user) {
        $html = '';
        $menu = self::loadMenu($id);
        foreach($menu as $mi) {
            $html .= $mi->render($user);
        }
        return $html;
    }

    public static function getMenuUnauth() {
        global $DB;
        $html = '';
        if($DB->valid()) {
            $rows = $DB->prepare('SELECT * FROM menu WHERE flags & :f ORDER BY pos')
                        ->bind('f', self::FLAG_IS_INFO)
                        ->execute_all();
        } else {
            $rows = [json_decode(MENU_NO_DB)];
        }
        foreach($rows as $row) {
            $mi = new MenuItem($row);
            $html .= $mi->render(null);
        }
        return $html;
    }

    public static function getList($flt = [], $ord = 'text', $lim = '') {
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
        $total = $calc ? intval($DB->select_scalar("SELECT FOUND_ROWS()")) : count($rows);
        foreach($rows as $row) {
            $ret[] = $obj ? new MenuItem($row, false) : ($fld ? intval($row[$fld]) : $row);
        }
        self::$total = $total;
        return $ret;
    }
}

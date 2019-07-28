<?php
class PageManager {
    public $args    = [];
    public $folder  = '';
    public $script  = '';
    public $action  = '';
    public $id      = 0;
    public $flags   = 0;

    public $title = '';
    public $descr = '';
    public $page = '';
    public $ver     = 1;
    public $mime    = '';
    public $url     = '';
    public $method  = '';
    public $userAgent = '';
    public $remoteIp  = '0.0.0.0';
    public $lang    = 'ru';
    public $lang_id = 1;
    public $locale  = 'ru_RU';
    public $javascriptVars = [];
    public $javaScripts    = [];
    public $styleSheets    = [];
    public $headers        = [];
    public $acl            = [];
    public $acl_level      = 0;
    public $download       = '';

    public $user = null;

    public $html     = '';
    public $request_body  = '';
    public $request  = null;
    public $rendered = false;
    public $executed = false;
    public $isScript = false;

    public $data = []; // Page data
    public $templates = []; // Page templates

    static $languages = [];

    static $main_tags = [
        'descr',
        'author',
        'locale',
        'title',
        'page',
        'lang',
        'ver'
    ];


    // HTML template RegExpressions
    const REG_TEMPLATE = '/(<%_(\w+)_%>)/';
    const REG_VARIABLE = '/(<%([\w_\.]+)%>)/';

    const FLAG_NO_MAIN    = 0x01;
    const FLAG_NO_LOGIN   = 0x02;
    const FLAG_AND_RIGHTS = 0x04;
    const FLAG_NO_ADMIN   = 0x08;

    public static $flags_nm = [
        ['f' => PageManager::FLAG_NO_MAIN,    'n' => 'Без головного шаблону', 'i' => 'fa fa-home text-primary',      'o' => 'fa fa-times text-danger' ],
        ['f' => PageManager::FLAG_NO_LOGIN,   'n' => 'Без авторизації',       'i' => 'fa fa-id-card text-secondary', 'o' => 'fa fa-times text-danger' ],
        ['f' => PageManager::FLAG_AND_RIGHTS, 'n' => 'Усі права',             'i' => 'fa fa-plus-circle text-info',  'o' => '' ],
        ['f' => PageManager::FLAG_NO_ADMIN,   'n' => 'Лише права',            'i' => 'fa fa-briefcase text-danger',  'o' => '' ],
    ];

    const ACL_READ   = 0x01;
    const ACL_EDIT   = 0x02;
    const ACL_DELETE = 0x04;
    const ACL_PAGE   = 0x08;

    public static $levels = [
        ['l' => self::ACL_READ,   't' => 'Read',   'n' => 'read', 'i'=> 'eye'],
        ['l' => self::ACL_EDIT,   't' => 'Edit',   'n' => 'edit', 'i'=> 'pencil'],
        ['l' => self::ACL_DELETE, 't' => 'Delete', 'n' => 'del',  'i'=> 'trash'],
        ['l' => self::ACL_PAGE,   't' => 'Page',   'n' => 'page', 'i'=> 'file-o']
    ];

    public static $dbg = [];
    public $debug = '';
    public static $pidFile = '';

    public function __construct($url, $self = false) {
        global $DB, $PG, $PM, $argv;

        $cron = $url == -1;


        self::$languages = Language::initCache();

        date_default_timezone_set('Europe/Kiev');

        $this->ver = time();

        $PM = $this;

        if(!$cron) {
            $this->url = 'http' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 's' : '') . '://' . $_SERVER['SERVER_NAME'];
            $this->headers = getallheaders();
            $this->userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
            $this->remoteIp = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
            $this->method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';

            $hdr = isset($this->headers['Accept-Language']) ? $this->headers['Accept-Language'] : 'uk';
            $this->evalLanguage($hdr);
            //$this->setLanguageCookie($days = 10);

            // API body ( see $this->showAll() )
            $this->request = new stdClass();

            // Papse URL
            $this->args = explode('/', $url);
            if(count($this->args) > 1 && $this->args[0] == '') array_shift($this->args);
            $this->script = $this->args ? array_shift($this->args) : '';
            if($this->isLangCode($this->script)) {
                $this->useLocale($this->script);
                $this->script = $this->args ? array_shift($this->args) : '';
            }
            if(!$this->isScriptOnly()) {
                $this->folder = $this->script;
                $this->script = $this->args ? $this->args[0] : '';
                if($this->script == '' || !$this->validScript()) {
                    $ok = false;
                    if(!$this->folder) $this->folder = 'view';
                    if($this->validFolder()) { // try index.php
                        $this->script = 'index';
                        $ok = $this->validScript();
                    }
                    if(!$ok) {
                        $this->script = $this->folder;
                        $this->folder = 'view';
                    }
                } else {
                    if($this->args) array_shift($this->args); // remove script
                }
            }
            $this->isScript = $this->isScriptOnly();

            if($this->folder == 'view' && $this->script == 'index') {
                //$this->redirect('/orders');
                //return;
            }
        } else {
            $this->isScript = true;
            $this->script = array_shift($argv);
            $this->args = $argv;
        }

        // Connect to bases
        $DB = new Database();
        //$PG = Database::PostgreSql();

        // Database error handling
        if(!$DB->valid()) $this->onDbError($DB, $cron);
        //if(!$PG->valid()) $this->onDbError($PG, $cron);
        $this->lang_id = Language::initCache($this->lang);

        // if(!$cron) {
        //     $this->title = _("DNK-Bilet");
        //     $this->descr = _("Concert agency DNK. Since 1996");
        //     $this->set('navText', $this->title);
        // }

        // Get current user
        $this->user = $cron ? CUser::get(0) : CUser::getCurrentUser();

        if($cron) return;

        // Read page info from DB
        $this->readPage();

        $this->request_body = file_get_contents("php://input");
        $this->request = json_decode($this->request_body);

        // Show login page, if not authorized
        if($this->user->id == 0 && !$this->allowAll() && !$this->isScript) {
            //$this->renderLogin(); - it's Ok
        }

        $acc = $this->hasAccess();

        if(!$this->isScript && !$acc) {
            //$this->redirect('/access');
            //return;
        }

        // $this->addJsVar('user_current', json_encode($this->user));
        $this->userAclVar();
        $this->localesVar();

        // $this->checkPassword();

        $run = false;
        try {
            $run = $this->runPhpScript();
            //self::debug($this, 'Me');
        }
        catch(Exception $e) {
            self::$dbg[] = "Exception : " . $e->getMessage();
        }
        if(!$run && $this->notPage() && !$this->script) {
            $this->folder = 'view';
            $this->script = 'index';
        }
        $this->render();

    }

    private function onDbError($db, $cron) {
        if($this->isScript) {
            if($cron) {
                $this->html .= $db->db_drv . " error " . $db->error;
                return;
            }
            $this->setJson(['status' => _('DB Error'), 'error' => $db->error]);
            $this->output();
            die();
        }
        $this->renderDbError($db);
    }

    private function notPage() {
        if($this->folder != 'view') {
            if($this->folder != 'event') return false;
        }
        $t = $this->getTemplate('');
        return empty($t);
    }

    private function readPage() {
        global $DB;
        if(!$DB->valid()) return;
        $row = $DB->prepare("SELECT id, title, descr, flags FROM pages WHERE folder=:f AND script=:s")
                    ->bind('f', $this->folder)
                    ->bind('s', $this->script)
                    ->execute_row();
        if($row) {
            foreach($row as $key => $val) {
                if(in_array($key, ['id','flags'])) {
                    $val = intval($val);
                } elseif($key == 'title') {
                    if(preg_match('/^\#(.*)$/', $val, $m)) {
                        $val = _($m[1]);
                    }
                    $this->set('navText', $val);
                }
                $this->$key = $val;
            }
            $this->title = "#{$row['id']} {$this->title}";
        }
        $this->readAcl();
    }

    private function readAcl() {
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

    private function hasAccess() {
        $cnt = 0;
        $lvl = 0;
        $sz  = count($this->acl);
        $fna = $this->testFlag(self::FLAG_NO_ADMIN);
        $this->acl_level = 0xFFFF;

        if($sz == 0) return true;
        if(isset($this->acl[0])) {
            $lvl = $this->acl[0];
            $cnt++;
        }
        foreach($this->user->rights as $rid) {
            if($rid == 1 && !$fna) return true;
            if(isset($this->acl[$rid])) {
                $cnt++;
                $lvl |= $this->acl[$rid];
            }
        }
        $this->acl_level = $lvl;
        if($this->testFlag(self::FLAG_AND_RIGHTS)) {
            return $cnt >= $sz;
        }
        return $cnt > 0;
    }

    public function redirectToLoginOrAccess() {
        $this->redirect($this->user->id ? '/access' : '/login');
    }

    public function assert($al = 0) { return ($this->acl_level & $al) == $al; }
    public function assertRead() { return $this->assert(self::ACL_READ); }
    public function assertEdit() { return $this->assert(self::ACL_EDIT); }
    public function assertDelete() { return $this->assert(self::ACL_DELETE); }
    public function assertPage() { return $this->assert(self::ACL_PAGE); }

    public function scriptAuthError() {
        $ret = new ScriptAnswer();
        $ret->auth();
        $this->setJson($ret);
    }

    public function testFlag($flag) {
        return ($this->flags & $flag) > 0;
    }

    public function setFlag($flag, $boolValue) {
        $this->flags = $boolValue ? ($this->flags | $flag) : ($this->flags & (~$flag));
    }

    public function allowAll() {
        if($this->folder == 'api') {
            return true;
        }
        if(in_array($this->script, ['dologin', 'logout', 'newpwd', 'info', 'restore'])) return true;
        return $this->testFlag(self::FLAG_NO_LOGIN);
    }

    public function validScript() {
        $php = $this->getModelPath();
        return is_file($php);
    }

    public function validFolder() {
        $folder = $this->getRootPrefix() . $this->folder;
        return is_dir($folder);
    }

    public function runPhpScript() {
        global $DB, $PG, $PM;
        // Main php script
        $php = $this->getRootPrefix() . 'view/main.php';
        if(is_file($php)) {
            require_once $php;
        }

        // Page php script
        $php = $this->getModelPath();
        $run = false;
        if(is_file($php)) {
            $run = true;
            $this->executed = $run;
            require_once $php;
        }
        return $run;
    }

    public function getActionPath($action = '') {
        $act = $action ? $action : $this->action;
        $prefix = $this->getRootPrefix();
        return $this->getBasePath($prefix) . '_' . $act . '.php';
    }

    public function isScriptOnly() {
        if($this->folder == '') return is_file($this->getModelPath());
        if($this->args) {
            $action = $this->getActionPath($this->args[0]);
            if(is_file($action)) return true;
        }
        return !is_file($this->getViewPath());
    }

    /**
     * Full root path
     * @return string
     */
    public function getRootPrefix() {
        return PATH_BASE . DIRECTORY_SEPARATOR;
    }

    /**
     * Full web root path
     * @return string
     */
    public function getWebRootPrefix() {
        return PATH_ROOT . DIRECTORY_SEPARATOR;
    }

    /**
     * Path = Prefix + Folder + Script
     * @param  string $prefix Path prefix
     * @return string
     */
    public function getBasePath($prefix, $delimiter = DIRECTORY_SEPARATOR) {
        return $prefix . $this->folder . $delimiter . $this->script;
    }

    /**
     * Path to View file (template)
     * @param  string $prefix
     * @return string
     */
    public function getViewPath($prefix = '') {
        if($prefix == '') $prefix = $this->getRootPrefix();
        return $this->getBasePath($prefix) . '.html';
    }

    /**
     * Path to folder View file (template)
     * @param  string $prefix
     * @return string
     */
    public function getFolderViewPath($page) {
        $prefix = $this->getRootPrefix();
        return $prefix . $this->folder . DIRECTORY_SEPARATOR . $page . '.html';
    }

    /**
     * Path to View template in view folder
     * @param  string $page
     * @return string
     */
    public function getBaseViewPath($page) {
        return $this->getRootPrefix() . 'view' .
            DIRECTORY_SEPARATOR . $page . '.html';
    }

    public function getImagePath($img = '') {
        return $this->getWebRootPrefix() . 'images' . DIRECTORY_SEPARATOR . $img;
    }

    /**
     * Javascript tag to inject in html
     * @return string
     */
    public function getJavascriptTag() {
        $jsBase = $this->getWebRootPrefix() . 'js' . DIRECTORY_SEPARATOR;
        $path = $this->getBasePath($jsBase) . '.js';
        if(is_file($path)) {
            $fv = filemtime($path);
            return '<script src="' . $this->getBasePath('/js/', '/') .
                    '.js?v=' . $fv . '"></script>';
        }
        //self::$dbg[] = "NO-JS:$path";
        return '';
    }

    /**
     * Path to Php file (model)
     * @param  string $prefix
     * @return string
     */
    public function getModelPath($prefix = '') {
        if($prefix == '') $prefix = $this->getRootPrefix();
        if($this->folder == '') {
            return $prefix . $this->script . '.php';
        }
        $add = $this->action == '' ? '' : "_{$this->action}";
        return $this->getBasePath($prefix) . $add . '.php';
    }

    public function replaceMainVariable($var) {
        $v = isset($this->$var) ? $this->$var : '';
        $this->html = str_replace("{{{$var}}}", $v, $this->html);
    }

    public function prepareJs() {
        $tmp = [];
        foreach($this->javaScripts as $js) {
            $path = PATH_ROOT . $js;
            $ext  = substr($js, 0, 4) == 'http';
            if(is_file($path) || $ext) {
                $fv = $ext ? $this->ver : filemtime($path);
                $tmp[] = "    <script src=\"{$js}?v={$fv}\"></script>";
            }
        }

        // JS Locale
        $loc = $this->getRootPrefix() . 'locale.php';
        if(is_file($loc)) {
            $fv = filemtime($loc);
            $tmp[] = "    <script src=\"/locale?v={$fv}\"></script>";
        }
        $this->set('scripts', implode("\n", $tmp));
    }

    public function prepareCss() {
        $tmp = [];
        foreach($this->styleSheets as $css) {
            $path = PATH_ROOT . $css;
            $ext  = substr($css, 0, 4) == 'http';
            if(is_file($path) || $ext) {
                $fv = $ext ? $this->ver : filemtime($path);
                $tmp[] = "    <link rel=\"stylesheet\" href=\"{$css}?v={$fv}\" />";
            }
        }
        $this->set('styles', implode("\n", $tmp));
    }

    /**
     * Create page in memory ($this->html)
     * @return string generated page
     */
    public function render() {
        if($this->rendered) return;
        $this->initMenu();
        $this->prepareJs();
        $this->prepareCss();
        $this->page = $this->getTemplate();
        if($this->testFlag(self::FLAG_NO_MAIN)) {
            $this->html = $this->page;
        } else {
            $this->html = $this->getTemplate('main');
        }

        foreach(self::$main_tags as $tag) {
            $this->replaceMainVariable($tag);
        }

        $this->injectJavascript();

        $this->debug = implode("\n", self::$dbg);
        $this->replaceMainVariable('debug');

        return $this->html;
    }

    public function getTemplate($page = '') {
        if($page == '' && $this->folder == '') {
            $page = $this->script;
        }
        $path = $page == '' ?
            $this->getViewPath() :
            $this->getBaseViewPath($page);
        if(!is_file($path) && $page != '') {
            $path = $this->getFolderViewPath($page);
        }
        $html = is_file($path) ? file_get_contents($path) : $this->page;
        $html = $this->replaceTemplates($html);
        $html = $this->replaceVariables($html);
        return $html;
    }

    public function prepareTemplate($tpl_name) {
        $tpl = $this->getTemplate($tpl_name);
        $repl = [];
        if(preg_match_all('/(##(\w+)##)/', $tpl, $m)) {
            foreach($m[1] as $ix => $dst) $repl[$dst] = $m[2][$ix];
        }
        $this->templates[$tpl_name] = [$tpl, $repl];
    }

    public function applyTemplate($tpl_name, $data) {
        $ret = "Wrong template $tpl_name";
        if(!array_key_exists($tpl_name, $this->templates)) return $ret;
        list($tpl, $repl) = $this->templates[$tpl_name];
        $ret = $tpl . '';
        foreach ($repl as $dst => $rep) {
            $src = '';
            if(is_object($data) && property_exists($data, $rep)) {
                $src = $data->$rep;
            }
            if(is_array($data) && array_key_exists($rep, $data)) {
                $src = $data[$rep];
            }
            $ret = str_replace($dst, $src, $ret);
        }
        unset($tpl, $repl);
        return $ret;
    }

    private function injectJavascript() {
        $jsFile = $this->getJsVarsTag() . $this->getJavascriptTag();
        if($jsFile == '') { return; }
        if(strpos($this->html, '</body>') !== FALSE) {
            $this->html = str_replace(
                '</body>',
                $jsFile . PHP_EOL . '</body>',
                $this->html
            );
        } elseif(strpos($this->html, '</html>') !== false) {
            $this->html = str_replace(
                '</html>',
                $jsFile . PHP_EOL . '</html>',
                $this->html
            );
        } else {
            $this->html .= PHP_EOL . $jsFile . PHP_EOL;
        }
    }

    private function replaceTemplates($html) {
        while(preg_match(self::REG_TEMPLATE, $html, $m)) {
            $subst = $this->getTemplate($m[2]);
            $html = str_replace($m[1], $subst, $html);
            //self::$dbg[] = "TPL({$m[1]})={$m[2]}";
        }
        return $html;
    }

    private function replaceVariables($html) {
        while(preg_match(self::REG_VARIABLE, $html, $m)) {
            $a = explode('.', $m[2]);
            $var = array_shift($a);
            $subst = $this->get($var);
            foreach($a as $sub) {
                if(isset($subst->$sub)) {
                    $subst = $subst->$sub;
                } else {
                    $subst = '';
                }
            }
            $html = str_replace($m[1], $subst, $html);
            //self::$dbg[] = "VAR({$m[1]})={$m[2]}";
        }
        return $html;
    }

    public function output() {
        if($this->mime) {
            header("Content-type: {$this->mime}");
        }
        if($this->download) {
            header("Content-Disposition: attachment; filename={$this->download}");
        }
        echo $this->html;
    }

    public function redirect($url) {
        header("Location: $url");
        die();
    }

    public function setJson($obj, $parsed = false) {
        if(self::$dbg) {
            if(is_array($obj)) {
                $obj['dbg'] = self::$dbg;
            } else {
                if(!$parsed) $obj->dbg = self::$dbg;
            }
        }
        $this->html = $parsed ? $obj : json_encode($obj, JSON_UNESCAPED_UNICODE);
        if($this->html == FALSE) {
            $this->html = "<!-- Json_error = " . self::getJsonError() .  "\n-->\n";
        }
        $this->mime = 'application/json; charset=utf-8';
        $this->rendered = true;
    }

    public function setDownload($txt, $mime = 'application/json; charset=utf-8', $filename = '') {
        $this->html = $txt;
        $this->mime = $mime;
        $this->download = $filename;
        $this->rendered = true;
    }

    public function setJavaScript($text) {
        $err = '';
        if(self::$dbg) {
            $err = "\n/* \n";
            foreach(self::$dbg as $v) {
                $err .= "$v\n";
            }
            $err .= " */\n";
        }
        $this->html = $text . $err;
        $this->mime = 'application/javascript; charset=utf-8';
        $this->rendered = true;
    }

    public function setImage($img, $mime) {
        $this->html = $img;
        $this->mime = $mime;
        $this->rendered = true;
    }

    public function set($name, $val) {
        $this->data[$name] = $val;
    }

    public function has($name) {
        return isset($this->data[$name]);
    }

    public function get($name, $default = false) {
        return isset($this->data[$name]) ? $this->data[$name] : $default;
    }

    public function remove($name) {
        if(isset($this->data[$name])) {
            delete($this->data[$name]);
            return true;
        }
        return false;
    }

    public function qwerty($txt) {
        $dict = [
            'ua' => [
                '`' => '\'',
                'q' => 'й',
                'w' => 'ц',
                'e' => 'у',
                'r' => 'к',
                't' => 'е',
                'y' => 'н',
                'u' => 'г',
                'i' => 'ш',
                'o' => 'щ',
                'p' => 'з',
                '[' => 'х',
                ']' => 'ї',
                'a' => 'ф',
                's' => 'і',
                'd' => 'в',
                'f' => 'а',
                'g' => 'п',
                'h' => 'р',
                'j' => 'о',
                'k' => 'л',
                'l' => 'д',
                ';' => 'ж',
                '\'' => 'є',
                '\\' => 'ґ',
                'z' => 'я',
                'x' => 'ч',
                'c' => 'с',
                'v' => 'м',
                'b' => 'и',
                'n' => 'т',
                'm' => 'ь',
                ',' => 'б',
                '.' => 'ю',
                '/' => '.',

                '~' => 'ʼ',
                'Q' => 'Й',
                'W' => 'Ц',
                'E' => 'У',
                'R' => 'К',
                'T' => 'Е',
                'Y' => 'Н',
                'U' => 'Г',
                'I' => 'Ш',
                'O' => 'Щ',
                'P' => 'З',
                '{' => 'Х',
                '}' => 'Ї',
                'A' => 'Ф',
                'S' => 'І',
                'D' => 'В',
                'F' => 'А',
                'G' => 'П',
                'H' => 'Р',
                'J' => 'О',
                'K' => 'Л',
                'L' => 'Д',
                ':' => 'ж',
                '"' => 'Є',
                '|' => 'Ґ',
                'Z' => 'Я',
                'X' => 'Ч',
                'C' => 'С',
                'V' => 'М',
                'B' => 'И',
                'N' => 'Т',
                'M' => 'Ь',
                '<' => 'Б',
                '>' => 'Ю',
                '?' => ','
            ],
            'ru' => []
        ];

        return strtr($txt, $dict['ua']);
    }

    public function userAclVar() {
        $acl = ['all'  => $this->acl_level];
        foreach(self::$levels as $l) {
            $acl[$l['n']] = ($this->acl_level & $l['l']) > 0;
        }
        $this->addJsVar('user_acl', $acl);
    }

    public function localesVar() {
        $this->addJsVar('locales', self::$languages);
    }

    // public function addJsVarJson($name, $val) {
    //     return $this->addJsVar($name, json_encode($val));
    // }

    /**
     * Добавление переменной Javascript
     * @param string $name Имя переменной
     * @param string $val Значение переменной
     * @param string $quote Символ до и после значения
     * @return $this
     */
    public function addJsVar($name, $val, $quote = '', $trans = '') {
        $var = $val;
        $qend = $quote;
        if(is_array($val) || is_object($val)) {
            $quote = '';
            $qend = '';
            if($trans) {
                $a_trans = is_string($trans) ? [$trans] : $trans;
                foreach($val as $k => $it) {
                    foreach($a_trans as $t) {
                        if(isset($it[$t])) {
                            $val[$k][$t] = _($val[$k][$t]);
                        }
                    }
                }
            }
            $var = json_encode($val, JSON_UNESCAPED_UNICODE);
        }
        if(is_bool($val)) $var = json_encode($val);
        if($quote == '"' || $quote == "'") {
            $var = str_replace($quote, ('\\' . $quote), $val);
        }
        switch($quote) {
            case '[': $qend = ']'; break;
            case '{': $qend = '}'; break;
            case '(': $qend = ')'; break;
        }
        $this->javascriptVars[$name] = $quote . $var . $qend;
        return $this;
    }

    public function getJsVarsTag() {
        $var = [];
        foreach ($this->javascriptVars as $key => $val) {
            $var[] = "$key = $val";
        }
        $vars = implode(",\n    ", $var);
        return $vars ? "<script>var $vars;</script>\n" : '';
    }

    public function addJScript($path) {
        $this->javaScripts[] = $path;
    }

    public function addCss($path) {
        $this->styleSheets[] = $path;
    }

    public function setArray($array) {
        $this->data = array_merge($this->data, $array);
    }

    private function staticPage($script, $noMain = true) {
        $this->script = $script;
        $this->folder = 'view';
        $this->setFlag(self::FLAG_NO_MAIN, $noMain);
        $this->runPhpScript();
        $this->render();
        $this->output();
        die();
    }

    private function renderLogin() {
        $this->title = _("Login");
        $this->staticPage('login', false);
    }

    private function renderDbError($DB) {
        $this->setArray([
            'err'  => $DB->error,
            'errn' => $DB->errno,
            'srv'  => $DB->serverAddr(),
            'db_error' => _("DB Error"),
            'drv'  => 'MySQL'
        ]);
        $this->staticPage('dberror');
    }

    public function initMenu() {
        $menu = '';
        if($this->user && $this->user->id) {
            $menu = MenuItem::getMenu(0, $this->user);
            $this->set('logout', MenuItem::LOGOUT_FORM);
        } else {
            $menu = MenuItem::getMenuUnauth();
        }
        $this->set('menu', $menu);
    }

    public function checkPassword() {
        if(!$this->user->hasTemporaryPassword()) return;
        if($this->isScript) return;
        if($this->script == 'newpwd') return;
        //$this->redirect('/newpwd');
    }

    static function sortLanguageWeight($a, $b)
    {
        return ($a['w'] > $b['w']) ? -1 : +1; // reverse sort
    }

    public function setLanguageCookie($days = 10) {
        $expire = mktime(23, 59, 59, date('m'), date('d') + $days, date('Y'));
        $domain = $_SERVER['SERVER_NAME'];
        setcookie('lang', $this->lang, $expire, '/', $domain, isset($_SERVER['HTTPS']));
    }

    public function setLangId() {
        $lid = 0;
        switch($this->lang) {
            case 'uk': $lid = 1; break;
            case 'ru': $lid = 2; break;
            case 'en': $lid = 3; break;
        }
        if($lid) $this->lang_id = $lid;
    }

    public function useLocale($lang) {
        if(is_object($lang) && is_a($lang, 'Language')) {
            $lng = $lang;
        } else {
            $lng = Language::get($lang);
            if($lng->i == 0) return false;
        }

        $this->lang = $lng->l;
        $this->locale = $lng->loc;
        $this->lang_id = $lng->i;

        $locale = $this->locale . '.utf8';
        // putenv("LANGUAGE=");
        // putenv("LC_ALL=$locale");
        // setlocale(LC_ALL, $locale);
        // bindtextdomain("dnk", PATH_TEXT);
        // textdomain("dnk");
        return true;
    }

    public function isLangCode($code) {
        foreach(self::$languages as $lng) {
            if($lng->l == $code) return true;
        }
        return false;
    }

    public function evalLanguage($hdr) {
        foreach($_COOKIE as $k => $v) {
            if($k == 'lang') {
                if($this->useLocale($v)) return;
            }
        }
        $languages = explode(',', $hdr);
        $arr = [];
        foreach($languages as $lng_str) {
            if(preg_match('/([\w\-]+)(\;q=([\d\.]+))*/', $lng_str, $m)) {
                $w = isset($m[3]) ? floatval($m[3]) : 1.0;
                $arr[] = ['l' => $m[1], 'w' => $w];
            }
        }
        usort($arr, ['PageManager', 'sortLanguageWeight']);
        foreach($arr as $k => $v) {
            $lng = $v['l'];
            if($this->useLocale($lng)) return;
        }

        $this->useLocale(Language::getDefault());
    }

    public function initBsGrid() {
        $this->addCss('/vendor/bs_grid/jquery.bs_grid.css');
        $this->addCss('/vendor/bs_grid/jquery.bs_pagination.css');
        $this->addCss('/vendor/bs_grid/jquery.jui_filter_rules.bs.css');

        $this->addJScript('/vendor/bs_grid/uk.js');
        $this->addJScript('/vendor/bs_grid/jquery.bs_grid.js');
        $this->addJScript('/vendor/bs_grid/bs_pg.' . $this->lang . '.js');
        $this->addJScript('/vendor/bs_grid/jquery.bs_pagination.js');
        $this->addJScript('/vendor/bs_grid/jui.' . $this->lang . '.js');
        $this->addJScript('/vendor/bs_grid/jquery.jui_filter_rules.js');
    }

    public static function debug($obj, $name = '') {
        $v = $obj;
        $v = json_encode($obj, JSON_UNESCAPED_UNICODE);
        if($name) $v = "$name=$v";
        self::$dbg[] = $v;
    }

    public static function pidLock($lckFile = 'alert.loc', $skip = 3600, $admin_tg = 0) {
        if(!$lckFile) return true;
        if(file_exists($lckFile)) {
            $crt = filectime($lckFile);
            $fp = fopen($lckFile, 'r');
            if($fp) {
                $pid = fgets($fp);
                fclose($fp);
            } else {
                $pid = 0;
            }
            if($pid && file_exists("/proc/$pid")) {
                $tm = time() - $crt;
                $dt = date('Y-m-d H:i:s', $crt);
                $msg = "Previous script ($pid) works since $dt ($tm sec.)";
                if($tm > $skip) {
                    $u = CUser::byTelegram($admin_tg);
                    $id = Telegram::sendMessage($u, "$lckFile ($pid) works $tm sec.");
                    $msg .= ", sms_id = $id";
                }
                Info($msg);
                return false;
            }
        }
        $pid = getmypid();
        $fp = fopen($lckFile, 'w');
        if($fp) {
            fwrite($fp, "$pid");
            fclose($fp);
            self::$pidFile = $lckFile;
            return true;
        }
        $msg = "$lckFile ($pid) can't create lock";
        $id = Telegram::sendMessage($admin_tg, $msg);
        $msg .= ", sms_id = $id";
        Info($msg);
        return false;
    }

    public static function pidUnLock() {
        if(self::$pidFile && file_exists(self::$pidFile)) unlink(self::$pidFile);
    }

    public static function getUploadErrorMessage($err_code) {
        switch(intval($err_code)) {
            case UPLOAD_ERR_INI_SIZE:
                return "Розмір прийнятого файла перебільшив максимально припустимий розмір.";
            case UPLOAD_ERR_FORM_SIZE:
                return "Розмір завантажуємого файла перебільшив значення MAX_FILE_SIZE, вказанне у HTML-формі.";
            case UPLOAD_ERR_PARTIAL:
                return "Завантажуваний файл був отриман лише частково.";
            case UPLOAD_ERR_NO_FILE:
                return "Файл не було завантажено.";
            case UPLOAD_ERR_NO_TMP_DIR:
                return "Відсутня тимчасова тека.";
            case UPLOAD_ERR_CANT_WRITE:
                return "Не вдалось зберегти файл на диск.";
            case UPLOAD_ERR_EXTENSION:
                return "Доповнення зупинило завантаження файла.";
            default:
                return 'Невідома помилка завантаження';
        }
    }

    public static function getJsonError($err_code = null) {
        if($err_code === null) $err_code = json_last_error();
        switch($err_code) {
            case JSON_ERROR_NONE : return 'Ошибок нет';
            case JSON_ERROR_DEPTH : return 'Достигнута максимальная глубина стека';
            case JSON_ERROR_STATE_MISMATCH : return 'Неверный или некорректный JSON';
            case JSON_ERROR_CTRL_CHAR : return 'Ошибка управляющего символа, возможно неверная кодировка';
            case JSON_ERROR_SYNTAX : return 'Синтаксическая ошибка';
            case JSON_ERROR_UTF8 : return 'Некорректные символы UTF-8, возможно неверная кодировка';
            case JSON_ERROR_RECURSION : return 'Одна или несколько зацикленных ссылок в кодируемом значении';
            case JSON_ERROR_INF_OR_NAN : return 'Одно или несколько значений NAN или INF в кодируемом значении';
            case JSON_ERROR_UNSUPPORTED_TYPE : return 'Передано значение с неподдерживаемым типом';
            case JSON_ERROR_INVALID_PROPERTY_NAME : return 'Имя свойства не может быть закодировано';
            case JSON_ERROR_UTF16 : return 'Некорректный символ UTF-16, возможно некорректно закодирован';
            default: 'Unknown error';
        }
    }
}


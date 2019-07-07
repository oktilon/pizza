<?php
class Language {
    public $i = 0;
    public $l = '';
    public $n = '';
    public $n_loc = '';
    public $loc = '';

    public static $cache = [];

    public function __construct($i = 0, $l = 'un', $n = 'Unk', $n_loc = 'Unk', $loc = 'un_UN') {
        $this->i = $i;
        $this->l = $l;
        $this->n = $n;
        $this->n_loc = $n_loc;
        $this->loc = $loc;
    }

    public static function initCache($lang = '') {
        if(count(self::$cache) == 0) {
            self::$cache = [
                new Language(1, 'uk', 'Ukr', 'Укр', 'uk_UA'),
                new Language(2, 'ru', 'Rus', 'Рус', 'ru_RU'),
                new Language(3, 'en', 'Eng', 'Eng', 'en_US'),
            ];
        }
        return self::$cache;
    }

    public static function get($id) {
        if(!count(self::$cache)) self::initCache();
        foreach (self::$cache as $lng) {
            if(is_numeric($id) && $lng->i == intval($id)) return $lng;
            if(is_string($id) && ($lng->l == $id || $lng->loc == $id)) return $lng;
        }
        return new Language();
    }

    public static function getDefault() {
        if(!count(self::$cache)) self::initCache();
        return self::$cache[0];
    }


    public static function getId($lang) {
        if(!count(self::$cache)) self::initCache();
        foreach(self::$cache as $lng) {
            if($lng->l == $lang) return $lng->id;
        }
        return 1;
    }

    public function getJson($ext = null) {
        $ret = new stdClass();
        foreach($this as $k => $v) $ret->$k = $v;
        return $ret;
    }
    public function getSimple() { return $this->getJson(); }

    public static function getArray() {
        if(!count(self::$cache)) self::initCache();
        return self::$cache;
    }
}

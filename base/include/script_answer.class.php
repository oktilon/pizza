<?php
class ScriptAnswer {
    public $status = 'wrong params';
    private $code = 0;

    const STATUS_OK = 'ok';

    public function __construct($arg = '') {
        if(is_string($arg) && $arg) $this->status = $arg;
        elseif(is_array($arg)) {
            foreach($arg as $k => $v) {
                if(is_string($k)) $this->$k = $v;
                else $this->$v = 0;
            }
        }
    }

    public function ok() {
        $this->status = self::STATUS_OK;
    }

    public function db($q, $code = 500) {
        global $DB;
        $this->status = $q ? self::STATUS_OK : $DB->error;
        $this->code = $code;
    }

    public function error($msg = 'error', $code = 500) {
        $this->status = $msg;
        $this->code = $code;
    }

    public function auth($code = 401) {
        $this->status = 'access denied';
        $this->code = $code;
    }

    public function getCode() {
        return $this->code;
    }

    public function exception(Exception $ex) {
        $this->status = $ex->getMessage();
        $this->code = $ex->getCode();
    }
}

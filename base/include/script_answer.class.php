<?php
class ScriptAnswer {
    public $status = 'wrong params';

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

    public function db($q) {
        global $DB;
        $this->status = $q ? self::STATUS_OK : $DB->error;
    }

    public function error($msg = 'error') {
        $this->status = $msg;
    }

    public function exception(Exception $ex) {
        $this->status = $ex->getMessage();
    }
}

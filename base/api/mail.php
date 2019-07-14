<?php
    $run = false;
    if($this->args) {
        $this->action = array_shift($this->args);
        $run = $this->runPhpScript();
    }

    if(!$run) {
        $ret = new ScriptAnswer('Wrong action');
        http_response_code(400);
        $this->setJson($ret);
    }


<?php
    $ret = new ScriptAnswer('Action not found');
    $this->set('ret', $ret);

    if($this->args) {
        $this->action = array_shift($this->args);
        $this->runPhpScript();
    }

    $this->setJson($this->get('ret'));


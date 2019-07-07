<?php
    $ret = new ScriptAnswer();

    $ret->data = Menu::getList();
    $ret->ok();

    $this->setJson($ret);
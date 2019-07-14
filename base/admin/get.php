<?php
    $ret = new ScriptAnswer();

    $info = $DB->prepare("SELECT * FROM info")->execute_all();

    $ret->menu = Menu::getList();
    $ret->ingr = Ingridient::getList();
    $ret->data = [];
    foreach($info as $row) $ret->data[$row['id']] = $row['val'];
    $ret->ok();

    $this->setJson($ret);
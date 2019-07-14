<?php
    use \Firebase\JWT\JWT;
    $ret = new ScriptAnswer();
    $pass  = property_exists($this->request, 'h') ? $this->request->h : '';//base64_encode('fJYkscCuZX');
    $login = property_exists($this->request, 'l') ? $this->request->l : '';

    $ret->jwt = '';
    $ret->ingr = [];
    $ret->data = [];
    $usr = CUser::loginUser($login, $pass, $this);

    if(!is_a($usr, 'CUser')) {
        $ret->error($usr);
    } else {
        $ret->ok();

        $token = array(
            "id"   => $usr->id,
            "sub"  => $usr->login,
            "name" => $usr->first_name,
            "roles" => $usr->rights,
            "iat" => time(),
            "exp" => time() + 3600
        );

        if($usr->id > 0) {
            $info = $DB->prepare("SELECT * FROM info")->execute_all();

            //$ret->menu = Menu::getList();
            $ret->ingr = Ingridient::getList();
            foreach($info as $row) $ret->data[$row['id']] = $row['val'];
        }

        $jwt = JWT::encode($token, JWT_PRIVATE_KEY, 'RS256');
        $ret->jwt = $jwt;
    }

    $this->setJson($ret);
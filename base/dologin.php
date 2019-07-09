<?php
    use \Firebase\JWT\JWT;
    $ret = new ScriptAnswer();
    $pass  = isset($_POST['h']) ? $_POST['h'] : base64_encode('fJYkscCuZX');
    $login = isset($_POST['l']) ? $_POST['l'] : 'kristina';

    $ret->jwt = '';
    $usr = CUser::loginUser($login, $pass, $this);

    if(!is_a($usr, 'CUser')) {
        $ret->error($usr);
    } else {
        $ret->ok();

        $token = array(
            "sub"  => $usr->login,
            "name" => $usr->first_name,
            "roles" => $usr->rights,
            "iat" => time(),
            "exp" => time() + 3600
        );

        $jwt = JWT::encode($token, JWT_PRIVATE_KEY, 'RS256');
        $ret->jwt = $jwt;
    }

    $this->setJson($ret);
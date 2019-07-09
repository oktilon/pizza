<?php
    $ret = new ScriptAnswer();
    $pass  = isset($_POST['h']) ? $_POST['h'] : '';
    $login = isset($_POST['l']) ? $_POST['l'] : '';

    $ret->error(CUser::loginUser($login, $pass, $this));

    use \Firebase\JWT\JWT;

    $key = "example_key";
    $token = array(
        "iss" => "http://example.org",
        "aud" => "http://example.com",
        "iat" => 1356999524,
        "nbf" => 1357000000
    );
    
    /**
     * IMPORTANT:
     * You must specify supported algorithms for your application. See
     * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
     * for a list of spec-compliant algorithms.
     */
    $jwt = JWT::encode($token, $key);
    // $decoded = JWT::decode($jwt, $key, array('HS256'));
    
    $ret->jwt = $jwt;    

    $this->setJson($ret);

    {"status":"Помилка логіна або пароля!","jwt":""}
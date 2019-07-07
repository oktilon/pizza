<?php
    $url = isset($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : '/';
    $this->set('back', $url);
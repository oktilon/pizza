<?php
    $imgBase = $this->getImagePath();
    $empty = $imgBase . 'none.png';
    $mimes = [
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg'
    ];

    $type = $this->args ? array_shift($this->args) : '';
    $id   = $this->args ? intval(array_shift($this->args)) : 0;
    switch($type) {
        case 'm':
            $imgBase .= 'menu' . DIRECTORY_SEPARATOR . $id . '.';
            break;

        case 'c':
            $imgBase .= 'content' . DIRECTORY_SEPARATOR . $id . '.';
            break;
    }

    $mime = '';
    foreach($mimes as $ext => $m) {
        $img = $imgBase . $ext;
        if(is_file($img)) {
            $imgBase = $img;
            $mime = $m;
        }
    }
    if(!$mime) {
        $imgBase = $empty;
        $mime = 'image/png';
    }

    $this->setImage(file_get_contents($imgBase), $mime);
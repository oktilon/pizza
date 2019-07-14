<?php
$red   = 'R0lGODlhAQABAPAAAP8AAAAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==';
$trans = 'R0lGODlhAQABAPAAAAAAAAAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==';

$ret = $red;

try {
    $file = $this->args ? array_shift($this->args) : '';
    if(preg_match('/(\w+)\.gif/', $file, $m)) {
        $ok = Email::trackMail($m[1]);
        $ret = $trans;
    }
}
catch(Exception $ex) {
    error_log("track mail exception " . $ex->getMessage());
}


header ("Content-Type: image/gif");
die(base64_decode($ret));

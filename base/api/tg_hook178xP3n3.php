<?php

$ret = $this->get('ret');

$body   = file_get_contents("php://input");
$update = json_decode($body);
$upid   = 0;
$mid    = 0;
$chat_id = 0;
$user_id = 0;
$tid   = 0;

try {
    $upid = isset($update->update_id) ? $update->update_id : 0;
    if($upid == 0) throw new Exception('No UpdateId');
    $msg = isset($update->message) ? $update->message : new stdClass();
    $mid = isset($msg->message_id) ? $msg->message_id : 0;
    if($mid == 0) throw new Exception('No MessageId');
    if(isset($msg->chat)) {
        $chat_id = $msg->chat->id;
    }

    $txt = '';
    if(isset($msg->text)) {
        $tid |= Telegram::TYPE_TEXT;
        $txt = $msg->text;
    }
    if(isset($msg->photo)) {
        $tid |= Telegram::TYPE_PHOTO;
    }

    if($mid > 0) {
        $q = $DB->prepare("INSERT INTO tg_incoming
                (msg_id, update_id, chat_id, type_id, msg, src)
                VALUES
                (:mid, :upd, :cid, :tid, :msg, :src)
                ON DUPLICATE KEY UPDATE
                    update_id = :upd,
                    chat_id = :cid,
                    type_id = :tid,
                    msg = :msg,
                    src = :src")
            ->bind('mid', $mid)
            ->bind('upd', $upid)
            ->bind('cid', $chat_id)
            ->bind('tid', $tid)
            ->bind('msg', $txt)
            ->bind('src', $body)
            ->execute();
        $ret->ok();
    } else {
        $ret->error('No message');
    }

    // Parse TEXT
    if($tid & Telegram::TYPE_TEXT) {
        $ans = '';
        if($ans) Telegram::sendMessage($chat_id, $ans);
    }
}
catch(Exception $ex) {
    syslog(LOG_ERR, "telegram exception " . $ex->getMessage());
    $log = 'Exception : ' . $ex->getMessage();
    $ret->error('exception');
    $ret->exception = $ex->getMessage();
}

$this->set('ret', $ret);
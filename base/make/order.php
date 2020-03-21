<?php
    $ret = new ScriptAnswer();

    $tg_icon = [
        'pizza'    => '🍕',
        'desert'   => '🍫',
        'fastfood' => '🍔',
        'drink'    => '🍻',
    ];

    try {
        $ret->req = $this->request;
        if(!property_exists($this->request, 'd')) throw new Exception('Invalid request', 400);
        $body = $this->request->d;
        $data = json_decode(urldecode(base64_decode($body)));

        if(!property_exists($data, 'f') ||
            !property_exists($data, 'c') ||
            !property_exists($data, 'd')) throw new Exception('Invalid arguments', 400);
        $form = $data->f;
        $cart = $data->c;

        if(!is_object($form) ||
            !property_exists($form, 'fio') ||
            !property_exists($form, 'adr') ||
            !property_exists($form, 'phone')) throw new Exception('Invalid arguments', 400);
        if(!is_array($cart) || count($cart) == 0)  throw new Exception('Invalid arguments', 400);

        $sign = array_key_exists('signature', $this->headers) ? $this->headers['signature'] : '';
        $timeSpan = abs(time() - $data->d);

        if($timeSpan > 1200) throw new Exception('Wrong query date', 400);
        $dt = date(ORDER_FMT, $data->d);
        $key = ORDER_KEY;
        $fm = base64_encode("{$dt}{$body}{$key}");

        $key = md5($fm);
        if($key != $sign) throw new Exception('Wrong signature', 400);

        $q = $DB->prepare("INSERT INTO orders (ip, name, adr, phone, note) VALUES (:i, :n, :a, :p, :e)")
                ->bind('i', ip2long($this->remoteIp))
                ->bind('n', $form->fio)
                ->bind('a', $form->adr)
                ->bind('p', $form->phone)
                ->bind('e', '')
                ->execute();
        if(!$q) throw new Exception("Store error: " . $DB->error, 500);

        $ret->oid = $DB->lastInsertId();
        if(!$ret->oid) throw new Exception("Store error. Oid=0", 500);

        $order_email = '';
        $order_user = '';
        $rows = $DB->prepare("SELECT * FROM info")->execute_all();
        foreach($rows as $row) {
            switch($row['id']) {
                case 'order': $order_email = $row['val']; break;
                case 'telegram': $order_user = $row['val']; break;
            }
        }
        if($order_email == '' && $order_user == '') throw new Exception("Empty recipients list", 500);

        $tbl = '';
        $tot = 0;
        $msg = [
            "✉️ Заказ №{$ret->oid}",
            "👤 *{$form->fio}*",
            "📞 *{$form->phone}*"
        ];
        if(!empty(trim($form->adr))) {
            $msg[] = "🏠 {$form->adr}";
        }
        $valid_count = 0;
        foreach($cart as $item) {
            $prc = new Price($item->p);
            $mnu = new Menu($prc->prod);
            if($prc->id == 0 || $mnu->id == 0) continue;
            $valid_count++;
            $DB->prepare("INSERT INTO order_items (ord, item, mnu, cnt) VALUES (:o, :i, :m, :c)")
                ->bind('o', $ret->oid)
                ->bind('i', $prc->id)
                ->bind('m', $mnu->id)
                ->bind('c', $item->c)
                ->execute();
            $txt = "{$mnu->name} ($prc->name)";
            $sum = $prc->price * $item->c;
            $tot += $sum;
            $tbl .= "<tr>
                <td>{$txt}</td>
                <td>{$prc->price}</td>
                <td>{$item->c}</td>
                <td>{$sum}</td>
            </tr>";

            $msg[] = $tg_icon[$mnu->kind] . " {$txt} +{$item->c}";
        }

        $msg[] = "💲 $tot грн.";

        if($valid_count == 0) throw new Exception('Empty order', 500);

        if($order_email != '') {
            $data = [
                'oid' => $ret->oid,
                'dt'  => date('d.m.Y H:i:s'),
                'fio' => $form->fio,
                'phone' => $form->phone,
                'adr' => $form->adr,
                'tot' => $tot,
                'tbl' => $tbl
            ];

            $tmp = EmailTemplate::get('order');

            $ok = mail($order_email, $tmp->getSubject($data), $tmp->getBody($data));

            //$ok = EmailTemplate::sendTemplate($order_email, 'order', $data, 1, 2);
            $ret->m = $ok ? 'ok' : 'err';
            if(!$ok) {
                $ret->me = error_get_last()['message'];
            }
        } else {
            $ret->m = 'no';
        }

        if($order_user != '') {
            $mid = Telegram::sendMessage($order_user, implode("\n", $msg));
            $ret->t = $mid > 0 ? 'ok' : 'err';
            if($mid == 0) {
                $ret->te = Telegram::$error;
                $ret->tr = Telegram::$result;
                $ret->ta = Telegram::$return;
            }
        } else {
            $ret->t = 'no';
        }

        $ret->ok();

    }
    catch(Exception $ex) {
        $ret->error($ex->getMessage());
        $code = $ex->getCode();
        if($code > 0) http_response_code($code);
    }

    $this->setJson($ret);
<?php
    $url = isset($_SERVER['REDIRECT_URL']) ? filter_var($_SERVER['REDIRECT_URL'], FILTER_SANITIZE_SPECIAL_CHARS) : '';
    $uri = 'http' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 's' : '') . '://' . $_SERVER['SERVER_NAME'];
    $headers = getallheaders();
    $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $remoteIp = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';

    $menu = 'main';
    switch($url) {
        case '':
        case '/':
            $menu = '';
            break;

        case '/pizza':
            $menu = 'pizza';
            break;

    }

?><!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="keywords" content="youssef, pizza, order, dnepr, пицца, заказ пиццы днепр, доставка пиццы">
	<meta name="description" content="пицца, сендвичи, шаурма, хот-дог, люля кебаб в лаваше с доставкой по городу Днепр. Большой выбор пиццы. Заказывай прямо сейчас.">
	<meta name="author" content="Order Pizza Youssef">
	<title>Order Pizza Youssef</title>
	<link rel="shortcut icon" href="/images/favicon.png" type="image/x-icon">
	<link rel="stylesheet" type="text/css" href="/css/style.css">
	<!--[if IE 7]>
		<link rel="stylesheet" type="text/css" href="css/ie7.css">
	<![endif]-->
	<!--[if IE 6]>
		<link rel="stylesheet" type="text/css" href="css/ie6.css">
	<![endif]-->
</head>
<body>
	<div id="header"> <!-- start of header -->
		<span class="signboard"></span>
		<ul id="infos">
			<li class="home">
				<a href="/">НА ГЛАВНУЮ</a>
			</li>
			<li class="phone">
				<a href="/contact">093 713 5868</a>
			</li>
			<li class="address">
				<a href="/contact">mail@orderpizza.dp.ua</a>
			</li>
		</ul>
		<h1><a href="/" id="logo2">Youssef</a></h1>
        <?php if($menu) {
        <ul id="navigation">
            <li><a href="http://orderpizza.dp.ua/entree.html"><span>Fast food</span></a></li>
            <li class="main current"><a href="http://orderpizza.dp.ua/menu.html"><span>Pizza</span></a></li>
            <li><a href="http://orderpizza.dp.ua/desserts.html"><span>Desserts</span></a></li>
            <li><a href="http://orderpizza.dp.ua/drinks.html"><span>Drinks</span></a></li>
        </ul> <!-- /#navigation -->

	</div> <!-- end of header -->

	<div id="body"> <!-- start of content -->
		<ul id="featured"> <!-- start of featured -->
			<li class="main">
				<a href="/pizza"></a>
			</li>
			<li class="drinks">
				<a href="/drinks"></a>
			</li>
			<li class="entree">
				<a href="/entree"></a>
			</li>
			<li class="desserts">
				<a href="/desserts"></a>
			</li>
		</ul> <!-- end of featured -->
        <div class="mn-pizza"><b>{</b> Pizza <b>}</b></div>
	</div> <!-- end of content -->
	<div id="footer"> <!-- start of footer -->
		<ul class="advertise">
			<li class="delivery">
				<h2>Голодно? Мы доставим</h2>
				<a href="/menu.html">Посмотри меню</a>
			</li>
			<li class="event">
				<h2>Устроим праздник!</h2>
				<p>Раскрась<br> вечеринку нашими блюдами</p>
			</li>
			<li class="connect">
				<h2>Давай дружить!</h2>
				<br>
				<a href="/index.html#" target="_blank" class="fb" title="Facebook"></a>
				<a href="https://www.instagram.com/orderpizza.dp.ua/" target="_blank" class="twitr" title="Twitter"></a>
			</li>
		</ul>
		<div>
			<ul class="navigation">
				<li class="selected"><a href="http://orderpizza.dp.ua/index.html">Главная</a></li>
				<li><a href="http://orderpizza.dp.ua/booking.html">Заказать онлайн</a></li>
				<li><a href="http://orderpizza.dp.ua/blog.html">Блог</a></li>
				<li><a href="http://orderpizza.dp.ua/about.html">О нас</a></li>
				<li class="last"><a href="http://orderpizza.dp.ua/contact.html">Контакты</a></li>
			</ul>
			<span>© Copyright 2017. All Rights Reserved.</span>
		</div>
	</div> <!-- end of footer -->
<div class="cumf_bt_form_wrapper" style="display:none">
<form id="contact_us_mail_feedback" action="http://orderpizza.dp.ua/oldTi9QvqM6ytokU9Q8ylQq" method="post">
    <fieldset>
        <!-- Form Name -->
        <legend>Contact Us</legend>
        <!-- Text input-->
        <div class="cumf_bt_form-group">
            <label class="col-md-4 cumf_bt_control-label" for="cumf_bt_name">name</label>
            <div class="col-md-4">
                <input id="cumf_bt_name" name="cumf_bt_name" type="text" placeholder="your name" class="cumf_bt_form-control cumf_bt_input-md
" required="1">
                <span class="cumf_bt_help-block">Please enter your name</span>
            </div>
        </div>
        <!-- Text input-->
        <div class="cumf_bt_form-group">
            <label class="col-md-4 cumf_bt_control-label" for="cumf_bt_email">your email</label>
            <div class="col-md-4">
                <input id="cumf_bt_email" name="cumf_bt_email" type="text" placeholder="enter your email" class="cumf_bt_form-control cumf_bt
_input-md" required="1">
                <span class="cumf_bt_help-block">please enter your email</span>
            </div>
        </div>
        <!-- Textarea -->
        <div class="cumf_bt_form-group">
            <label class="col-md-4 cumf_bt_control-label" for="cumf_bt_message">your message</label>
            <div class="col-md-4">
                <textarea class="cumf_bt_form-control" id="cumf_bt_message" name="cumf_bt_message">Message goes here</textarea>
            </div>
        </div>
        <input type="submit" id="cumf_bt_submit" value="Send">
    </fieldset>
</form>
</div>

</body></html>
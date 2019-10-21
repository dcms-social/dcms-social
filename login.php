<?
include_once 'sys/inc/start.php';
include_once 'sys/inc/compress.php';
include_once 'sys/inc/sess.php';
include_once 'sys/inc/home.php';
include_once 'sys/inc/settings.php';
include_once 'sys/inc/db_connect.php';
include_once 'sys/inc/ipua.php';
include_once 'sys/inc/fnc.php';
include_once 'sys/inc/shif.php';
$show_all=true; // показ для всех
$input_page=true;
include_once 'sys/inc/user.php';
only_unreg();



if (isset($_GET['id']) && isset($_GET['pass']))
{

	if (mysql_result(mysql_query("SELECT COUNT(*) FROM `user` WHERE `id` = '".intval($_GET['id'])."' AND `pass` = '".shif($_GET['pass'])."' LIMIT 1"), 0)==1)
	{
		$user = get_user($_GET['id']);
		$_SESSION['id_user'] = $user['id'];

		mysql_query("UPDATE `user` SET `date_aut` = ".time()." WHERE `id` = '$user[id]' LIMIT 1");
		mysql_query("UPDATE `user` SET `date_last` = ".time()." WHERE `id` = '$user[id]' LIMIT 1");
		mysql_query("INSERT INTO `user_log` (`id_user`, `time`, `ua`, `ip`, `method`) values('$user[id]', '$time', '$user[ua]' , '$user[ip]', '0')");
	}
	else $_SESSION['err'] = 'Неправильный логин или пароль';
}
elseif (isset($_POST['nick']) && isset($_POST['pass']))
{
	if (mysql_result(mysql_query("SELECT COUNT(*) FROM `user` WHERE `nick` = '".my_esc($_POST['nick'])."' AND `pass` = '".shif($_POST['pass'])."' LIMIT 1"), 0))
	{
		$user = mysql_fetch_assoc(mysql_query("SELECT `id` FROM `user` WHERE `nick` = '".my_esc($_POST['nick'])."' AND `pass` = '".shif($_POST['pass'])."' LIMIT 1"));
		$_SESSION['id_user'] = $user['id'];
		$user = get_user($user['id']);
		
		// сохранение данных в COOKIE
		if (isset($_POST['aut_save']) && $_POST['aut_save'])
		{
			setcookie('id_user', $user['id'], time()+60*60*24*365);
			setcookie('pass', cookie_encrypt($_POST['pass'],$user['id']), time()+60*60*24*365);
		}

		mysql_query("UPDATE `user` SET `date_aut` = '$time', `date_last` = '$time' WHERE `id` = '$user[id]' LIMIT 1");
		mysql_query("INSERT INTO `user_log` (`id_user`, `time`, `ua`, `ip`, `method`) values('$user[id]', '$time', '$user[ua]' , '$user[ip]', '1')");
	}
	else $_SESSION['err'] = 'Неправильный логин или пароль';
}
elseif (isset($_COOKIE['id_user']) && isset($_COOKIE['pass']) && $_COOKIE['id_user'] && $_COOKIE['pass'])
{
	if (mysql_result(mysql_query("SELECT COUNT(*) FROM `user` WHERE `id` = ".intval($_COOKIE['id_user'])." AND `pass` = '".shif(cookie_decrypt($_COOKIE['pass'],intval($_COOKIE['id_user'])))."' LIMIT 1"), 0)==1)
	{
		$user = get_user($_COOKIE['id_user']);
		$_SESSION['id_user'] = $user['id'];
		mysql_query("UPDATE `user` SET `date_aut` = '$time', `date_last` = '$time' WHERE `id` = '$user[id]' LIMIT 1");
		$user['type_input'] = 'cookie';
	}
	else
	{
		$_SESSION['err'] = 'Ошибка авторизации по COOKIE';
		setcookie('id_user');
		setcookie('pass');
	}
}
else $_SESSION['err'] = 'Ошибка авторизации';


if (!isset($user))
{
	header('Location: /aut.php');
	exit;
}


// Пишем ip пользователя

if (isset($ip2['add']))mysql_query("UPDATE `user` SET `ip` = ".ip2long($ip2['add'])." WHERE `id` = '$user[id]' LIMIT 1");
else mysql_query("UPDATE `user` SET `ip` = null WHERE `id` = '$user[id]' LIMIT 1");
if (isset($ip2['cl']))mysql_query("UPDATE `user` SET `ip_cl` = ".ip2long($ip2['cl'])." WHERE `id` = '$user[id]' LIMIT 1");
else mysql_query("UPDATE `user` SET `ip_cl` = null WHERE `id` = '$user[id]' LIMIT 1");
if (isset($ip2['xff']))mysql_query("UPDATE `user` SET `ip_xff` = ".ip2long($ip2['xff'])." WHERE `id` = '$user[id]' LIMIT 1");
else mysql_query("UPDATE `user` SET `ip_xff` = null WHERE `id` = '$user[id]' LIMIT 1");
if ($ua)mysql_query("UPDATE `user` SET `ua` = '".my_esc($ua)."' WHERE `id` = '$user[id]' LIMIT 1");

// Непонятная сессия
mysql_query("UPDATE `user` SET `sess` = '$sess' WHERE `id` = '$user[id]' LIMIT 1");

// Тип браузера
mysql_query("UPDATE `user` SET `browser` = '" . ($webbrowser == true ? "wap" : "web") . "' WHERE `id` = '$user[id]' LIMIT 1");

// Проверяем на схожие ники
$collision_q = mysql_query("SELECT * FROM `user` WHERE `ip` = '$iplong' AND `ua` = '".my_esc($ua)."' AND `date_last` > '".(time()-600)."' AND `id` <> '$user[id]'");

while ($collision = mysql_fetch_assoc($collision_q))
{
	if (mysql_result(mysql_query("SELECT COUNT(*) FROM `user_collision` WHERE `id_user` = '$user[id]' AND `id_user2` = '$collision[id]' OR `id_user2` = '$user[id]' AND `id_user` = '$collision[id]'"), 0) == 0)
	mysql_query("INSERT INTO `user_collision` (`id_user`, `id_user2`, `type`) values('$user[id]', '$collision[id]', 'ip_ua_time')");
}

/*
========================================
Рейтинг
========================================
*/
if (isset($user) && $user['rating_tmp']>1000)
{
	// Счетчик активности
	$col = $user['rating_tmp']; 
	
	// Делим на 100 что бы получить процент
	$col = $col / 1000; 
	
	// Округляем
	$col = intval($col); 
	
	// Прибавляем % рейтинга
	mysql_query("update `user` set `rating` = '" . ($user['rating'] + $col) . "' where `id` = '$user[id]' limit 1"); 
	
	// Оповещаем
	$_SESSION['message'] = "Поздравляем! Вам за вашу активность начислено $col% рейтинга!"; 
	
	// Вычисляем остаток счетчика активности
	$col = $user['rating_tmp'] - ($col * 1000); 
	
	// Сбрасываем
	mysql_query("update `user` set `rating_tmp` = '$col' where `id` = '$user[id]' limit 1"); 
}

if (isset($_GET['return']))
header('Location: '.urldecode($_GET['return']));
else header("Location: /my_aut.php?".SID);

exit;
?>
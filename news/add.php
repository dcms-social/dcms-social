<?
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';

user_access('adm_news',null,'index.php?'.SID);

// Переменные по умолчанию
if (isset($_POST['view']))
{
	$news['title'] = $_POST['title'];
	$news['msg'] = $_POST['msg'];
	$news['link'] = $_POST['link'];
	$news['id_user'] = $user['id'];
}else{
	$news['title'] = null;
	$news['msg'] = null;
	$news['link'] = null;
	$news['id_user'] = null;
}

if (isset($_POST['title']) && isset($_POST['msg']) && isset($_POST['link']) && isset($_POST['ok']))
{
	$title = esc($_POST['title'],1);
	$link = esc($_POST['link'],1);
	$msg = esc($_POST['msg'],1);

	if ($link != NULL && !preg_match('#^https?://#',$link) && !preg_match('#^/#i',$link))$link='/'.$link;
	 
	 if (strlen2($title)>50){$err = 'Слишком большой заголовок новости';}
	if (strlen2($title)<3){$err = 'Короткий заголовок';}
	$mat = antimat($title);
	if ($mat)$err[] = 'В заголовке новости обнаружен мат: '.$mat;
	
	if (strlen2($msg)>10024){$err='Содержиние новости слишком большое';}
	if (strlen2($msg)<2){$err='Новость слишком короткая';}
	$mat = antimat($msg);
	if ($mat)$err[]='В содержании обнаружен мат: '.$mat;
	
	$title = my_esc($_POST['title']);
	$msg = my_esc($_POST['msg']);if (!isset($err)){
	$ch = intval($_POST['ch']);
	$mn = intval($_POST['mn']);
	$main_time = time()+$ch*$mn*60*60*24;if ($main_time<=time())
	$main_time = 0;
	
	mysql_query("INSERT INTO `news` (`id_user`,`time`, `msg`, `title`, `main_time`, `link`) values('".$user['id']."','$time', '".$msg."', '$title', '$main_time', '$link')");
	mysql_query("update `user` set `news_read` = '0'");
	
	$news['id'] = mysql_insert_id();
	mysql_query("OPTIMIZE TABLE `news`");
	mysql_query("UPDATE `user` SET `news_read` = '0'");

	if (isset($_POST['mail'])) // Расслылка новостей на майл
	{
		$q = mysql_query("SELECT `ank_mail` FROM `user` WHERE `set_news_to_mail` = '1' AND `ank_mail` <> ''");
		while ($ank = mysql_fetch_assoc($q))
		{
			mysql_query("INSERT INTO `mail_to_send` (`mail`, `them`, `msg`) values('$ank[ank_mail]', 'Новости', '".trim(br(bbcode(links(stripcslashes(htmlspecialchars($msg))))))."')");
		}
	}
   	
	$_SESSION['message'] = 'Новость успешно создана';
	header("Location: news.php?id=$news[id]");
	exit;
	}
}

$set['title'] = 'Создание новости';
include_once '../sys/inc/thead.php';
title();
err();
aut(); // форма авторизации


if (isset($_POST['view']) && !isset($err))
{
		
	echo '<div class="main2">';
	echo text($news['title']);
	echo '</div>';

	echo'<div class="mess">';
	echo output_text($news['msg']) . '<br />';
	echo '</div>';
	
	if ($news['link'] != NULL)
	{
		echo '<div class="main">';
		echo '<a href="' . htmlentities($news['link'], ENT_QUOTES, 'UTF-8') . '">Подробности &rarr;</a><br />';
		echo '</div>';
	}
}

echo '<form class="mess" method="post" name="message" action="add.php">';
echo 'Заголовок новости:<br /><input name="title" size="16" maxlength="32" value="' . text($news['title']) . '" type="text" /><br />';

$msg2 = text($news['msg']);

if (is_file(H.'style/themes/'.$set['set_them'].'/altername_post_form.php'))
{
	include_once H.'style/themes/'.$set['set_them'].'/altername_post_form.php';
}else{
	echo 'Сообщение:' . $tPanel . '<textarea name="msg">' . $msg2 . '</textarea><br />';
}

echo 'Ссылка:<br /><input name="link" size="16" maxlength="64" value="' . text($news['link']) . '" type="text" /><br />';

echo 'Показывать на главной:<br />';
echo '<input type="text" name="ch" size="3" value="'.(isset($_POST['ch'])?"".intval($_POST['ch'])."":"1").'" />';
echo '<select name="mn">';
echo '  <option value="0" ' . (isset($_POST['mn']) && $_POST['mn'] == 0 ? "selected='selected'" : null) . '>   </option>';
echo '  <option value="1" ' . (isset($_POST['mn']) && $_POST['mn'] == 1 ? "selected='selected'" : null) . '>Дней</option>';
echo '  <option value="7" ' . (isset($_POST['mn']) && $_POST['mn'] == 7 ? "selected='selected'" : null) . '>Недель</option>';
echo '  <option value="31" ' . (isset($_POST['mn']) && $_POST['mn'] == 31 ? "selected='selected'" : null).'>Месяцев</option>';
echo '</select><br />';

echo '<input value="Просмотр" type="submit" name="view" /> ';
if (isset($_POST['view']))echo '<input value="Готово" type="submit" name="ok" />';
echo '</form>';


echo '<div class="foot">';
echo '<img src="/style/icons/str.gif" alt="*"> <a href="index.php">Новости</a><br />';
echo '</div>';

include_once '../sys/inc/tfoot.php';
?>
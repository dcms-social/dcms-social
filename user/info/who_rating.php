<?
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/user.php';


if (isset($user))$ank['id']=$user['id'];
if (isset($_GET['id']))$ank['id']=intval($_GET['id']);

$ank=get_user($ank['id']);
if(!$ank){header("Location: /index.php?".SID);exit;}
$user_id=$ank['id'];

if ((!isset($_SESSION['refer']) || $_SESSION['refer']==NULL)
&& isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']!=NULL &&
!preg_match('#info\.php#',$_SERVER['HTTP_REFERER']))
$_SESSION['refer']=str_replace('&','&amp;',preg_replace('#^http://[^/]*/#','/', $_SERVER['HTTP_REFERER']));


if (isset($_POST['rating']) && isset($user) && isset($_POST['msg']) && $user['id']!=$ank['id'] && $user['rating']>=2 && mysql_result(mysql_query("SELECT SUM(`rating`) FROM `user_voice2` WHERE `id_kont` = '$user[id]'"),0)>=0)
{
	$msg=mysql_real_escape_string($_POST['msg']);
	if (strlen($msg)<3)$err='Короткий Отзыв';
	if (strlen($msg)>1024)$err='Длиный Отзыв';
	elseif (mysql_result(mysql_query("SELECT COUNT(*) FROM `user_voice2` WHERE `id_user` = '$user[id]' AND `msg` = '".my_esc($msg)."' LIMIT 1"),0)!=0){$err='Ваш отзыв повторяется';}
	if (!isset($err))
	{
		$new_r=min(max(@intval($_POST['rating']),-2),2);
		mysql_query("DELETE FROM `user_voice2` WHERE `id_user` = '$user[id]' AND `id_kont` = '$ank[id]' LIMIT 1");
		echo $new_r;

		if ($new_r) 
		{
			if (mysql_result(mysql_query("SELECT COUNT(*) FROM `user_voice2` WHERE `id_user` = '$user[id]' AND `id_kont` = '$ank[id]' LIMIT 1"),0) == 0)
			{
				mysql_query("INSERT INTO `user_voice2` (`rating`, `id_user`, `id_kont`, `msg`, `time`) VALUES ('$new_r','$user[id]','$ank[id]', '$msg', '$time')");
				mysql_query("UPDATE `user` SET `rating` = '".($ank['rating'] + $new_r)."' WHERE `id` = '$ank[id]' LIMIT 1");
			}
			else
			{
				mysql_query("UPDATE `user_voice2` SET `rating` = '".$new_r."', msg = $msg, time = $time WHERE `id_user` = '$user[id]' AND `id_kont` = '$ank[id]' LIMIT 1");
			}
		}

		if ($new_r>0)
		mysql_query("INSERT INTO `mail` (`id_user`, `id_kont`, `msg`, `time`) values('0', '$ank[id]', '$user[nick] оставил о Вас [url=/user/info/who_rating.php]положительный отзыв[/url]', '$time')");
		if ($new_r<0)
		mysql_query("INSERT INTO `mail` (`id_user`, `id_kont`, `msg`, `time`) values('0', '$ank[id]', '$user[nick] оставил о Вас [url=/user/info/who_rating.php]негативный отзыв[/url]', '$time')");
		if ($new_r==0)
		mysql_query("INSERT INTO `mail` (`id_user`, `id_kont`, `msg`, `time`) values('0', '$ank[id]', '$user[nick] оставил о Вас [url=/user/info/who_rating.php]нейтральный отзыв[/url]', '$time')");
		mysql_query("UPDATE `user` SET `rating_tmp` = '".($user['rating_tmp']+1)."' WHERE `id` = '$user[id]' LIMIT 1");

		$_SESSION['message'] = 'Ваше мнение о пользователе успешно изменено';
	}
}


$set['title']=$ank['nick'].' - отзывы '; // заголовок страницы
include_once '../../sys/inc/thead.php';
title();
aut();
err();



if (isset($user))$ank['id']=$user['id'];
if (isset($_GET['id']))$ank['id']=intval($_GET['id']);

if (isset($user) && $user['id']!=$ank['id'] && $user['rating']>=2 && mysql_result(mysql_query("SELECT SUM(`rating`) FROM `user_voice2` WHERE `id_kont` = '$user[id]'"),0)>=0)
{
	echo "<b>Ваше отношение:</b><br />\n";
	// мое отношение к пользователю

	$my_r=intval(@mysql_result(mysql_query("SELECT `rating` FROM `user_voice2` WHERE `id_user` = '$user[id]' AND `id_kont` = '$ank[id]'"),0));
	echo "<form method='post' action='?id=$ank[id]&amp;$passgen'>\n";
	echo "<select name='rating'>\n";
	echo "<option value='2' ".($my_r==2?'selected="selected"':null).">Замечательное</option>\n";
	echo "<option value='1' ".($my_r==1?'selected="selected"':null).">Положительное</option>\n";
	echo "<option value='0' ".($my_r==0?'selected="selected"':null).">Нейтральное</option>\n";
	echo "<option value='-1' ".($my_r==-1?'selected="selected"':null).">Не очень...</option>\n";
	echo "<option value='-2' ".($my_r==-2?'selected="selected"':null).">Негативное</option>\n";
	echo "</select><br />\n";
	echo "Текст: <br />";
	echo "<textarea name=\"msg\"></textarea><br />";
	echo "<input type='submit' value='GO' />\n";
	echo "</form>\n";
	//echo "<br />\n";
}
elseif (isset($user) && $user['id'] != $ank['id'])
{
	echo "<div class='mess'>";
	echo 'Чтобы оставить отзыв, вам необходимо набрать 2 или более % рейтинга.';
	echo "</div>";
}



$k_post=mysql_result(mysql_query("SELECT COUNT(*) FROM `user_voice2` WHERE `id_kont` = '".$ank['id']."'"),0);
$k_page=k_page($k_post,$set['p_str']);
$page=page($k_page);
$start=$set['p_str']*$page-$set['p_str'];

echo "<table class='post'>\n";

if ($k_post==0)
{
	echo '<div class="mess">';
	echo "Нет положительных отзывов\n";
	echo '</div>';
}

$q=mysql_query("SELECT * FROM `user_voice2` WHERE `id_kont` = '$ank[id]' ORDER BY `time` DESC LIMIT $start, $set[p_str]");

while ($post = mysql_fetch_assoc($q))
{
	$ank=get_user($post['id_user']);

	// Лесенка дивов
	if ($num == 0)
	{
		echo '<div class="nav1">';
		$num = 1;
	}
	elseif ($num == 1)
	{
		echo '<div class="nav2">';
		$num = 0;
	}

	echo group($ank['id']) . " <a href='/info.php?id=$ank[id]'>$ank[nick]</a> \n";

	echo "".medal($ank['id'])." ".online($ank['id'])." (".vremja($post['time']).") <br />";


	echo "Отзыв:<br />\n";

	switch ($post['rating'])
	{
		case 2:echo "Замечательный<br />\n";	break;
		case 1:echo "Положительный<br />\n";	break;
		case 0:echo "Нейтральный<br />\n";	break;
		case -1:echo "Не очень...<br />\n";	break;
		case -2:echo "Негативный<br />\n";	break;
	}

	$msg=stripcslashes(htmlspecialchars($post['msg']));
	echo "<br />$msg\n";

	echo '</div>';
}

echo "</table>\n";


if ($k_page>1)str('who_rating.php?id='.$user_id.'&amp;',$k_page,$page); // Вывод страниц

include_once '../../sys/inc/tfoot.php';
?>
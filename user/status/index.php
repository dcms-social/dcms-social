<?
/*
=======================================
Статусы юзеров для Dcms-Social
Автор: Искатель
---------------------------------------
Этот скрипт распостроняется по лицензии
движка Dcms-Social. 
При использовании указывать ссылку на
оф. сайт http://dcms-social.ru
---------------------------------------
Контакты
ICQ: 587863132
http://dcms-social.ru
=======================================
*/
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/user.php';

 // Автор статусов
if (isset($_GET['id']))
$anketa=get_user(intval($_GET['id']));
else
$anketa=get_user($user['id']);

if (!$anketa['id']) { header("Location: /index.php"); exit; }

if (isset($_GET['reset'])){
$status=mysql_fetch_assoc(mysql_query("SELECT * FROM `status` WHERE `id` = '".intval($_GET['reset'])."' LIMIT 1"));
if ($status['id_user']==$user['id']){
mysql_query("UPDATE `status` SET `pokaz` = '0' WHERE `id_user` = '$user[id]'");
mysql_query("UPDATE `status` SET `pokaz` = '1' WHERE `id` = '$status[id]'");
$_SESSION['message'] = 'Статус упешно включен';
header("Location: index.php?id=$anketa[id]"); 
exit;
}
}

$set['title']='Статусы '.$anketa['nick'];
include_once '../../sys/inc/thead.php';
title();

err();

aut(); // форма авторизации



/*
==================================
Приватность станички пользователя
Запрещаем просмотр статусов
==================================
*/

	$uSet = mysql_fetch_array(mysql_query("SELECT * FROM `user_set` WHERE `id_user` = '$anketa[id]'  LIMIT 1"));
	$frend=mysql_result(mysql_query("SELECT COUNT(*) FROM `frends` WHERE (`user` = '$user[id]' AND `frend` = '$anketa[id]') OR (`user` = '$anketa[id]' AND `frend` = '$user[id]') LIMIT 1"),0);
	$frend_new=mysql_result(mysql_query("SELECT COUNT(*) FROM `frends_new` WHERE (`user` = '$user[id]' AND `to` = '$anketa[id]') OR (`user` = '$anketa[id]' AND `to` = '$user[id]') LIMIT 1"),0);

if ($anketa['id'] != $user['id'] && $user['group_access'] == 0)
{

	if (($uSet['privat_str'] == 2 && $frend != 2) || $uSet['privat_str'] == 0) // Начинаем вывод если стр имеет приват настройки
	{
		if ($anketa['group_access']>1)echo "<div class='err'>$anketa[group_name]</div>\n";
		echo "<div class='nav1'>";
		echo group($anketa['id'])." $anketa[nick] ";
		echo medal($anketa['id'])." ".online($anketa['id'])." ";
		echo "</div>";

		echo "<div class='nav2'>";
		avatar_ank($anketa['id']);
		echo "</div>";

	}
	
	
	if ($uSet['privat_str'] == 2 && $frend != 2) // Если только для друзей
	{
		echo '<div class="mess">';
		echo 'Просматривать статусы пользователя могут только его друзья!';
		echo '</div>';
		
		// В друзья
		if (isset($user))
		{
			echo '<div class="nav1">';
			if ($frend_new == 0 && $frend==0){
			echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?add=".$anketa['id']."'>Добавить в друзья</a><br />\n";
			}elseif ($frend_new == 1){
			echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?otm=$anketa[id]'>Отклонить заявку</a><br />\n";
			}elseif ($frend == 2){
			echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?del=$anketa[id]'>Удалить из друзей</a><br />\n";
			}
			echo "</div>";
		}
	include_once '../../sys/inc/tfoot.php';
	exit;
	}
	
	if ($uSet['privat_str'] == 0) // Если закрыта
	{
		echo '<div class="mess">';
		echo 'Пользователь запретил просматривать его статусы!';
		echo '</div>';
		
	include_once '../../sys/inc/tfoot.php';
	exit;
	}

}
	

echo "<div class='foot'>";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/info.php?id=$anketa[id]\">$anketa[nick]</a> | <b>Статусы</b>";
echo "</div>";




$k_post=mysql_result(mysql_query("SELECT COUNT(*) FROM `status` WHERE `id_user` = '".$anketa['id']."'"),0);
$k_page=k_page($k_post,$set['p_str']);
$page=page($k_page);
$start=$set['p_str']*$page-$set['p_str'];
$q=mysql_query("SELECT * FROM `status` WHERE `id_user` = '".$anketa['id']."' ORDER BY `id` DESC LIMIT $start, $set[p_str]");
echo "<table class='post'>\n";
if ($k_post==0)
{
echo "<div class='mess'>\n";
echo "Нет статусов\n";
echo "</div>\n";
}

while ($post = mysql_fetch_assoc($q))
{
$ank=mysql_fetch_assoc(mysql_query("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));
/*-----------зебра-----------*/ 
if ($num==0){
	echo '<div class="nav1">';
	$num=1;
}elseif ($num==1){
	echo '<div class="nav2">';
	$num=0;
}
/*---------------------------*/

echo '<div class="st_1"></div>';
echo '<div class="st_2">';
echo output_text($post['msg']);
echo "</div>";


echo "<a href='komm.php?id=$post[id]'><img src='/style/icons/bbl4.png' alt=''/>" . mysql_result(mysql_query("SELECT COUNT(*) FROM `status_komm` WHERE `id_status` = '$post[id]'"),0) . "</a> ";

if ($post['pokaz']==0){
	if (isset($user) && ($user['level']!=0 || $user['id']==$ank['id']))
		echo "[<a href=\"index.php?id=".$anketa['id']."&amp;reset=$post[id]\"><img src='/style/icons/ok.gif' alt=''/> вкл</a>]\n";

	if (isset($user) && ($user['level']>$ank['level'] || $user['level']!=0 || $user['id']==$ank['id']))
		echo " [<a href=\"delete.php?id=$post[id]\"><img src='/style/icons/delete.gif' alt=''/> удл</a>]\n";
}else{

	if (isset($user) && ($user['level']>$ank['level'] || $user['level']!=0 || $user['id']==$ank['id']))
		echo " <font color='green'>Установлен</font>\n";

	if (isset($user) && ($user['level']>$ank['level'] || $user['level']!=0 || $user['id']==$ank['id']))
		echo " [<a href=\"delete.php?id=$post[id]\"><img src='/style/icons/delete.gif' alt=''/> удл</a>]\n";
}

echo '</div>';
}
echo "</table>\n";


if ($k_page>1)str("index.php?id=".$anketa['id'].'&amp;',$k_page,$page); // Вывод страниц

echo "<div class='foot'>";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/info.php?id=$anketa[id]\">$anketa[nick]</a> | <b>Статусы</b>";
echo "</div>";
include_once '../../sys/inc/tfoot.php';
?>
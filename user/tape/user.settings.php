<?
/*
=======================================
Лента друзей для Dcms-Social
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

if (isset($user))$ank['id']=$user['id'];
if (isset($_GET['id']))$ank['id']=intval($_GET['id']);
$ank=get_user($ank['id']);
if(!$ank || $ank['id']==0){header("Location: /index.php?".SID);exit;}
only_reg();






$frend = mysql_fetch_array(mysql_query("SELECT * FROM `frends` WHERE `user` = '".$user['id']."' AND `frend` = '$ank[id]' AND `i` = '1'"));

if (isset($_POST['save'])){
 // Лента фото
if (isset($_POST['lenta_foto']) && ($_POST['lenta_foto']==0 || $_POST['lenta_foto']==1))
{
mysql_query("UPDATE `frends` SET `lenta_foto` = '".intval($_POST['lenta_foto'])."' WHERE `user` = '$user[id]' AND `frend` = '$ank[id]'");
}
 // Лента файлов
if (isset($_POST['lenta_obmen']) && ($_POST['lenta_obmen']==0 || $_POST['lenta_obmen']==1))
{
mysql_query("UPDATE `frends` SET `lenta_obmen` = '".intval($_POST['lenta_obmen'])."' WHERE `user` = '$user[id]' AND `frend` = '$ank[id]'");
}
 // Лента смены аватара
if (isset($_POST['lenta_avatar']) && ($_POST['lenta_avatar']==0 || $_POST['lenta_avatar']==1))
{
mysql_query("UPDATE `frends` SET `lenta_avatar` = '".intval($_POST['lenta_avatar'])."' WHERE `user` = '$user[id]' AND `frend` = '$ank[id]'");
}
 // Лента новых друзей
if (isset($_POST['lenta_frends']) && ($_POST['lenta_frends']==0 || $_POST['lenta_frends']==1))
{
mysql_query("UPDATE `frends` SET `lenta_frends` = '".intval($_POST['lenta_frends'])."' WHERE `user` = '$user[id]' AND `frend` = '$ank[id]'");
}
 // Лента статусов
if (isset($_POST['lenta_status']) && ($_POST['lenta_status']==0 || $_POST['lenta_status']==1))
{
mysql_query("UPDATE `frends` SET `lenta_status` = '".intval($_POST['lenta_status'])."' WHERE `user` = '$user[id]' AND `frend` = '$ank[id]'");
}
 // Лента оценок статуса
if (isset($_POST['lenta_status_like']) && ($_POST['lenta_status_like']==0 || $_POST['lenta_status_like']==1))
{
mysql_query("UPDATE `frends` SET `lenta_status_like` = '".intval($_POST['lenta_status_like'])."' WHERE `user` = '$user[id]' AND `frend` = '$ank[id]'");
}
 // Лента дневников
if (isset($_POST['lenta_notes']) && ($_POST['lenta_notes']==0 || $_POST['lenta_notes']==1))
{
mysql_query("UPDATE `frends` SET `lenta_notes` = '".intval($_POST['lenta_notes'])."' WHERE `user` = '$user[id]' AND `frend` = '$ank[id]'");
}
 // Лента форум
if (isset($_POST['lenta_forum']) && ($_POST['lenta_forum']==0 || $_POST['lenta_forum']==1))
{
mysql_query("UPDATE `frends` SET `lenta_forum` = '".intval($_POST['lenta_forum'])."' WHERE `user` = '$user[id]' AND `frend` = '$ank[id]'");
}


$_SESSION['message'] = 'Изменения успешно приняты';
header('Location: index.php');
exit;
}

$set['title']='Настройка ленты для '.$ank['nick'];
include_once '../../sys/inc/thead.php';
title();

err();
aut();

echo "<div id='comments' class='menus'>";
echo "<div class='webmenu'>";
echo "<a href='index.php'>Лента</a>";
echo "</div>"; 
echo "<div class='webmenu'>";
echo "<a href='settings.php'>Настройки</a>";
echo "</div>"; 
echo "</div>";


echo "<form action='?id=$ank[id]' method=\"post\">";
 // Лента друзей
echo "<div class='mess'>";
echo "Уведомления о новых друзьях $ank[nick].";
echo "</div>";

echo "<div class='nav1'>";
echo "<input name='lenta_frends' type='radio' ".($frend['lenta_frends']==1?' checked="checked"':null)." value='1' /> Да ";
echo "<input name='lenta_frends' type='radio' ".($frend['lenta_frends']==0?' checked="checked"':null)." value='0' /> Нет ";
echo "</div>";

 // Лента Дневников
echo "<div class='mess'>";
echo "Уведомления о новых дневниках $ank[nick].";
echo "</div>";

echo "<div class='nav1'>";
echo "<input name='lenta_notes' type='radio' ".($frend['lenta_notes']==1?' checked="checked"':null)." value='1' /> Да ";
echo "<input name='lenta_notes' type='radio' ".($frend['lenta_notes']==0?' checked="checked"':null)." value='0' /> Нет ";
echo "</div>";

 // Лента Форума
echo "<div class='mess'>";
echo "Уведомления о новых темах $ank[nick] в форуме.";
echo "</div>";

echo "<div class='nav1'>";
echo "<input name='lenta_forum' type='radio' ".($frend['lenta_forum']==1?' checked="checked"':null)." value='1' /> Да ";
echo "<input name='lenta_forum' type='radio' ".($frend['lenta_forum']==0?' checked="checked"':null)." value='0' /> Нет ";
echo "</div>";

 // Лента фото
echo "<div class='mess'>";
echo "Уведомления о новых фото $ank[nick].";
echo "</div>";

echo "<div class='nav1'>";
echo "<input name='lenta_foto' type='radio' ".($frend['lenta_foto']==1?' checked="checked"':null)." value='1' /> Да ";
echo "<input name='lenta_foto' type='radio' ".($frend['lenta_foto']==0?' checked="checked"':null)." value='0' /> Нет ";
echo "</div>";
 // Лента о смене аватара
echo "<div class='mess'>";
echo "Уведомления о смене аватаров $ank[nick].";
echo "</div>";

echo "<div class='nav1'>";
echo "<input name='lenta_avatar' type='radio' ".($frend['lenta_avatar']==1?' checked="checked"':null)." value='1' /> Да ";
echo "<input name='lenta_avatar' type='radio' ".($frend['lenta_avatar']==0?' checked="checked"':null)." value='0' /> Нет ";
echo "</div>";
 // Лента файлов
echo "<div class='mess'>";
echo "Уведомления о новых файлах $ank[nick].";
echo "</div>";

echo "<div class='nav1'>";
echo "<input name='lenta_obmen' type='radio' ".($frend['lenta_obmen']==1?' checked="checked"':null)." value='1' /> Да ";
echo "<input name='lenta_obmen' type='radio' ".($frend['lenta_obmen']==0?' checked="checked"':null)." value='0' /> Нет ";
echo "</div>";

 // Лента статусов
echo "<div class='mess'>";
echo "Уведомления о новых статусах $ank[nick].";
echo "</div>";

echo "<div class='nav1'>";
echo "<input name='lenta_status' type='radio' ".($frend['lenta_status']==1?' checked="checked"':null)." value='1' /> Да ";
echo "<input name='lenta_status' type='radio' ".($frend['lenta_status']==0?' checked="checked"':null)." value='0' /> Нет ";
echo "</div>";

 // Лента оценок статуса
echo "<div class='mess'>";
echo "Уведомления о \"Like\" к статусам друзей $ank[nick].";
echo "</div>";

echo "<div class='nav1'>";
echo "<input name='lenta_status_like' type='radio' ".($frend['lenta_status_like']==1?' checked="checked"':null)." value='1' /> Да ";
echo "<input name='lenta_status_like' type='radio' ".($frend['lenta_status_like']==0?' checked="checked"':null)." value='0' /> Нет ";
echo "</div>";

echo "<div class='main'>";
echo "<input type='submit' name='save' value='Сохранить' />";
echo "</div>";

echo "</form>";


	
include_once '../../sys/inc/tfoot.php';
?>
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

/* Бан пользователя */ 
if (mysql_result(mysql_query("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'chat' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0)!=0)
{
header('Location: /ban.php?'.SID);exit;
}


if (isset($user))mysql_query("DELETE FROM `chat_who` WHERE `id_user` = '$user[id]'");
mysql_query("DELETE FROM `chat_who` WHERE `time` < '".($time-120)."'");
if (isset($user) && isset($_GET['id']) && mysql_result(mysql_query("SELECT COUNT(*) FROM `chat_rooms` WHERE `id` = '".intval($_GET['id'])."'"),0)==1
&& isset($_GET['msg']) && mysql_result(mysql_query("SELECT COUNT(*) FROM `user` WHERE `id` = '".intval($_GET['msg'])."'"),0)==1)
{
$room=mysql_fetch_assoc(mysql_query("SELECT * FROM `chat_rooms` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1"));
$ank=mysql_fetch_assoc(mysql_query("SELECT * FROM `user` WHERE `id` = '".intval($_GET['msg'])."' LIMIT 1"));
if (isset($user))mysql_query("INSERT INTO `chat_who` (`id_user`, `time`,  `room`) values('$user[id]', '$time', '$room[id]')");
if ($set['time_chat']!=0)header("Refresh: $set[time_chat]; url=/chat/room/$room[id]/".rand(1000,9999).'/'); // автообновление
$set['title']='Чат - '.$room['name'].' ('.mysql_result(mysql_query("SELECT COUNT(*) FROM `chat_who` WHERE `room` = '$room[id]'"),0).')'; // заголовок страницы
include_once '../sys/inc/thead.php';
title();
echo "<a href='/info.php?id=$ank[id]'>Посмотреть анкету</a><br />\n";
echo "<form method=\"post\" action=\"/chat/room/$room[id]/".rand(1000,9999)."/\">\n";
echo "Сообщение:<br />\n<textarea name=\"msg\">$ank[nick], </textarea><br />\n";
echo "<label><input type=\"checkbox\" name=\"privat\" value=\"$ank[id]\" /> Приватно</label><br />\n";
if ($user['set_translit']==1)echo "<label><input type=\"checkbox\" name=\"translit\" value=\"1\" /> Транслит</label><br />\n";
echo "<input value=\"Отправить\" type=\"submit\" />\n";
echo "</form>\n";
echo "<div class=\"foot\">\n";
echo " <img src='/style/icons/str2.gif' alt='*'><a href=\"/chat/room/$room[id]/".rand(1000,9999)."/\">В комнату</a><br />\n";
echo " <img src='/style/icons/str2.gif' alt='*'><a href=\"/chat/\">Прихожая</a><br />\n";
echo "</div>\n";
include_once '../sys/inc/tfoot.php';
}
if (isset($_GET['id']) && mysql_result(mysql_query("SELECT COUNT(*) FROM `chat_rooms` WHERE `id` = '".intval($_GET['id'])."'"),0)==1)
{
$room=mysql_fetch_assoc(mysql_query("SELECT * FROM `chat_rooms` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1"));
if (isset($user))mysql_query("INSERT INTO `chat_who` (`id_user`, `time`,  `room`) values('$user[id]', '$time', '$room[id]')");
if ($set['time_chat']!=0)header("Refresh: $set[time_chat]; url=/chat/room/$room[id]/".rand(1000,9999).'/'); // автообновление
$set['title']='Чат - '.$room['name'].' ('.mysql_result(mysql_query("SELECT COUNT(*) FROM `chat_who` WHERE `room` = '$room[id]'"),0).')'; // заголовок страницы
include_once '../sys/inc/thead.php';
title();
include 'inc/room.php';
echo "<div class=\"foot\">\n";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/chat/\">Прихожая</a><br />\n";
echo "</div>\n";
include_once '../sys/inc/tfoot.php';
}
$set['title']='Чат - прихожая'; // заголовок страницы
include_once '../sys/inc/thead.php';
title();
include 'inc/admin_act.php';
err();
aut(); // форма авторизации
echo "<table class='post'>\n";
$q=mysql_query("SELECT * FROM `chat_rooms` ORDER BY `pos` ASC");
if ( mysql_num_rows($q) == 0 ) {
echo "  <div class='mess'>\n";
echo "Нет комнат\n";
echo "  </div>\n";
}
while ($room = mysql_fetch_assoc($q))
{
/*-----------зебра-----------*/if ($num==0){echo '<div class="nav1">';$num=1;}elseif ($num==1){echo '<div class="nav2">';$num=0;}/*---------------------------*/
echo "<img src='/style/themes/$set[set_them]/chat/14/room.png' alt='' /> ";


echo "<a href='/chat/room/$room[id]/".rand(1000,9999)."/'>$room[name] (".mysql_result(mysql_query("SELECT COUNT(*) FROM `chat_who` WHERE `room` = '$room[id]'"),0).")</a> \n";
if (user_access('chat_room'))echo "<a href='?set=$room[id]'><img src='/style/icons/edit.gif' alt='*' /></a> \n"; 
if ($room['opis']!=NULL)echo '<br />'.esc(trim(br(bbcode(smiles(links(stripcslashes(htmlspecialchars($room['opis']))))))))."<br />\n";





echo "   </div>\n";
}
echo "</table>\n";
echo "<div class=\"foot\">\n";
echo "<img src='/style/icons/str.gif' alt='*'> <a href='who.php'>Кто в чате?</a><br />\n"; 
echo "</div>\n";
include 'inc/admin_form.php';
include_once '../sys/inc/tfoot.php';
?>
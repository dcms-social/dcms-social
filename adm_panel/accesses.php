<?
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/adm_check.php';
include_once '../sys/inc/user.php';
user_access('adm_accesses',null,'index.php?'.SID);
adm_check();




if (isset($_GET['id_group']) && mysql_result(mysql_query("SELECT COUNT(*) FROM `user_group` WHERE `id` = '".intval($_GET['id_group'])."'"), 0))
{

$group=mysql_fetch_assoc(mysql_query("SELECT * FROM `user_group` WHERE `id` = '".intval($_GET['id_group'])."'"));

$set['title']=output_text('Группа "'.$group['name'].'" - привилегии'); // заголовок страницы
include_once '../sys/inc/thead.php';
title();
if (isset($_POST['accesses']))
{
mysql_query("DELETE FROM `user_group_access` WHERE `id_group` = '$group[id]'");

$q=mysql_query("SELECT * FROM `all_accesses`");

while ($post = mysql_fetch_assoc($q))
{
$type=$post['type'];
if (isset($_POST[$type]) && $_POST[$type]==1)
mysql_query("INSERT INTO `user_group_access` (`id_group`, `id_access`) VALUES ('$group[id]', '$post[type]')");

}

msg('Привилегии успешно изменены');
}
aut();
echo "<form method='post' action='?id_group=$group[id]&amp;$passgen'>\n";
$q=mysql_query("SELECT * FROM `all_accesses` ORDER BY `name` ASC");
while ($post = mysql_fetch_assoc($q))
{
echo "<label>";
echo "<input type='checkbox'".(mysql_result(mysql_query("SELECT COUNT(*) FROM `user_group_access` WHERE `id_group` = '$group[id]' AND `id_access` = '$post[type]' LIMIT 1"),0)==1?" checked='checked'":null)." name='$post[type]' value='1' />";
echo $post['name'];
echo "</label><br />\n";
}
echo "<input value='Применить' name='accesses' type='submit' />\n";
echo "</form>\n";



echo "<div class='foot'>\n";
echo "&laquo;<a href='accesses.php'>Группы</a><br />";
echo "&laquo;<a href='index.php'>Админка</a><br />";
echo "</div>\n";
include_once '../sys/inc/tfoot.php';
}

$set['title']='Группы пользователей'; // заголовок страницы
include_once '../sys/inc/thead.php';
title();

aut();



echo "<div class='menu'>\n";
$accesses=mysql_query("SELECT * FROM `user_group` ORDER BY `id` ASC");
while ($res = mysql_fetch_assoc($accesses))
{
echo "<a href='?id_group=$res[id]'>$res[name] (L$res[level], ".mysql_result(mysql_query("SELECT COUNT(*) FROM `user_group_access` WHERE `id_group` = '$res[id]'"),0).")</a><br />\n";
}
echo "</div>\n";






if (user_access('adm_panel_show')){
echo "<div class='foot'>\n";
echo "&laquo;<a href='index.php'>Админка</a><br />";
echo "</div>\n";}
include_once '../sys/inc/tfoot.php';
?>

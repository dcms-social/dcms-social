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
user_access('adm_forum_sinc',null,'index.php?'.SID);
adm_check();
$set['title']='Синхронизация таблиц форума';
include_once '../sys/inc/thead.php';
title();
err();
aut();


if (isset($_GET['ok']) && isset($_POST['accept']))
{
$d_r=0;$d_t=0;$d_p=0;


// удаление разделов
$q=mysql_query("SELECT `id`,`id_forum` FROM `forum_r`");
while ($razd=mysql_fetch_assoc($q))
{
if (mysql_result(mysql_query("SELECT COUNT(*) FROM `forum_f` WHERE `id` = '$razd[id_forum]'"), 0)==0)
{
mysql_query("DELETE FROM `forum_r` WHERE `id` = '$razd[id]' LIMIT 1");
$d_r++;
}

}

// удаление тем
$q=mysql_query("SELECT `id`, `id_razdel`, `id_user` FROM `forum_t`");
while ($them=mysql_fetch_assoc($q))
{
if (mysql_result(mysql_query("SELECT COUNT(*) FROM `forum_r` WHERE `id` = '$them[id_razdel]'"), 0)==0 || mysql_result(mysql_query("SELECT COUNT(*) FROM `user` WHERE `id` = '$them[id_user]'"), 0)==0)
{
mysql_query("DELETE FROM `forum_t` WHERE `id` = '$them[id]' LIMIT 1");
$d_t++;
}
}

// удаление постов
$q=mysql_query("SELECT `id`, `id_them`, `id_user` FROM `forum_p`");
while ($post=mysql_fetch_assoc($q))
{
if (mysql_result(mysql_query("SELECT COUNT(*) FROM `forum_t` WHERE `id` = '$post[id_them]'"), 0)==0 || mysql_result(mysql_query("SELECT COUNT(*) FROM `user` WHERE `id` = '$post[id_user]'"), 0)==0)
{
mysql_query("DELETE FROM `forum_p` WHERE `id` = '$post[id]' LIMIT 1");
$d_p++;
}
}
msg("Удалено разделов: $d_r, тем: $d_t, постов: $d_p");
}

echo "<form method=\"post\" action=\"?ok\">\n";
echo "<input value=\"Начать\" name='accept' type=\"submit\" />\n";
echo "</form>\n";

echo "* В зависимости от количества сообщений и тем, данное действие может занять длительное время.<br />\n";
echo "** Рекомендуется использовать только в случах расхождений счетчиков форума с реальными данными<br />\n";

if (user_access('adm_panel_show')){
echo "<div class='foot'>\n";
echo "&laquo;<a href='/adm_panel/'>В админку</a><br />\n";
echo "</div>\n";
}

include_once '../sys/inc/tfoot.php';
?>
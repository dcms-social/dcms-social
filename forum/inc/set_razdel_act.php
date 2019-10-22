<?
if (isset($_GET['act']) && isset($_GET['ok']) && $_GET['act']=='set' && isset($_POST['name']))
{

$name=$_POST['name'];
$opis=$_POST['opis'];

if (strlen2($name)<3)$err='Слишком короткое название';
if (strlen2($name)>32)$err='Слишком днинное название';
$name=my_esc($name);
$opis=my_esc($opis);

if (!isset($err)){
$razd=mysql_fetch_assoc(mysql_query("SELECT * FROM `forum_r` WHERE `id` = '".intval($_GET['id_razdel'])."' AND `id_forum` = '".intval($_GET['id_forum'])."' LIMIT 1"));
admin_log('Форум','Разделы',"Переименование раздела '$razd[name]' в '$name'");

mysql_query("UPDATE `forum_r` SET `name` = '$name', `opis` = '$opis' WHERE `id` = '$razdel[id]' LIMIT 1");
$razdel=mysql_fetch_assoc(mysql_query("SELECT * FROM `forum_r` WHERE `id` = '$razdel[id]' LIMIT 1"));
msg('Изменения успешно приняты');
}
}

$razd=mysql_fetch_assoc(mysql_query("SELECT * FROM `forum_r` WHERE `id` = '".intval($_GET['id_razdel'])."' AND `id_forum` = '".intval($_GET['id_forum'])."' LIMIT 1"));

if (isset($_GET['act']) && isset($_GET['ok']) && $_GET['act']=='mesto' && isset($_POST['forum']) && is_numeric($_POST['forum'])
&& mysql_result(mysql_query("SELECT COUNT(*) FROM `forum_f` WHERE `id` = '".intval($_POST['forum'])."'"),0)==1)
{
$forum_new['id']=intval($_POST['forum']);
$forum_old=$forum;
mysql_query("UPDATE `forum_p` SET `id_forum` = '$forum_new[id]' WHERE `id_forum` = '$forum[id]' AND `id_razdel` = '$razdel[id]'");
mysql_query("UPDATE `forum_t` SET `id_forum` = '$forum_new[id]' WHERE `id_forum` = '$forum[id]' AND `id_razdel` = '$razdel[id]'");
mysql_query("UPDATE `forum_r` SET `id_forum` = '$forum_new[id]' WHERE `id_forum` = '$forum[id]' AND `id` = '$razdel[id]'");


$forum=mysql_fetch_assoc(mysql_query("SELECT * FROM `forum_f` WHERE `id` = '$forum_new[id]' LIMIT 1"));


admin_log('Форум','Разделы',"Перенос раздела '$razd[name]' из подфорума '$forum_old[name]' в '$forum[name]'");

msg('Раздел успешно перенесен');

}

if (isset($_GET['act']) && isset($_GET['ok']) && $_GET['act']=='delete')
{

mysql_query("DELETE FROM `forum_r` WHERE `id` = '$razdel[id]'");
mysql_query("DELETE FROM `forum_t` WHERE `id_razdel` = '$razdel[id]'");
mysql_query("DELETE FROM `forum_p` WHERE `id_razdel` = '$razdel[id]'");
admin_log('Форум','Разделы',"Удаление раздела '$razd[name]' из подфорума '$forum[name]'");
msg('Раздел успешно удален');
err();
aut();
echo "<a href=\"/forum/$forum[id]/\">В Подфорум</a><br />\n";
echo "<a href=\"/forum/\">В форум</a><br />\n";
include_once '../sys/inc/tfoot.php';
}
?>
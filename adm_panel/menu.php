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
include_once '../sys/inc/icons.php'; // Иконки главного меню
user_access('adm_menu',null,'index.php?'.SID);
adm_check();
$set['title']='Главное меню';
include_once '../sys/inc/thead.php';
title();

$opendiricon=opendir(H.'style/icons');
while ($icons=readdir($opendiricon))
{
// запись всех тем в массив
if (preg_match('#^\.|default.png#',$icons))continue;
$icon[]=$icons;
}
closedir($opendiricon);


if (isset($_POST['add']) && isset($_POST['name']) && $_POST['name']!=NULL && isset($_POST['url']) && $_POST['url']!=NULL && isset($_POST['counter']))
{
$name=esc(stripcslashes(htmlspecialchars($_POST['name'])));
$url=esc(stripcslashes(htmlspecialchars($_POST['url'])));
$counter=esc(stripcslashes(htmlspecialchars($_POST['counter'])));
$pos=mysql_result(mysql_query("SELECT MAX(`pos`) FROM `menu`"), 0)+1;

$icon=preg_replace('#[^a-z0-9 _\-\.]#i', null, $_POST['icon']);
mysql_query("INSERT INTO `menu` (`name`, `url`, `counter`, `pos`, `icon`) VALUES ('$name', '$url', '$counter', '$pos', '$icon')");
msg('Ссылка успешно добавлена');
}



if (isset($_POST['add']) && isset($_POST['name']) && $_POST['name']!=NULL && isset($_POST['counter']) && isset($_POST['type']) && $_POST['type']=='razd')
{
$name=esc(stripcslashes(htmlspecialchars($_POST['name'])));
$url=esc(stripcslashes(htmlspecialchars($_POST['url'])));
$counter=esc(stripcslashes(htmlspecialchars($_POST['counter'])));
$pos=mysql_result(mysql_query("SELECT MAX(`pos`) FROM `menu`"), 0)+1;

$icon=preg_replace('#[^a-z0-9 _\-\.]#i', null, $_POST['icon']);
mysql_query("INSERT INTO `menu` (`type`, `name`, `url`, `counter`, `pos`, `icon`) VALUES ('razd', '$name', '$url', '$counter', '$pos', '$icon')");
msg('Ссылка успешно добавлена');
}



if (isset($_POST['change']) && isset($_GET['id']) && isset($_POST['name']) && $_POST['name']!=NULL && isset($_POST['url']) && isset($_POST['counter']))
{
$id=intval($_GET['id']);
$name=esc(stripcslashes(htmlspecialchars($_POST['name'])));
$url=esc(stripcslashes(htmlspecialchars($_POST['url'])));
$counter=esc(stripcslashes(htmlspecialchars($_POST['counter'])));
$icon=preg_replace('#[^a-z0-9 _\-\.]#i', null, $_POST['icon']);
mysql_query("UPDATE `menu` SET `name` = '$name', `url` = '$url', `counter` = '$counter', `icon` = '$icon' WHERE `id` = '$id' LIMIT 1");
msg('Пункт меню успешно изменен');
}

if (isset($_GET['id']) && isset($_GET['act']) && mysql_result(mysql_query("SELECT COUNT(*) FROM `menu` WHERE `id` = '".intval($_GET['id'])."'"),0))
{

$menu=mysql_fetch_assoc(mysql_query("SELECT * FROM `menu` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1"));
if ($_GET['act']=='up')
{
mysql_query("UPDATE `menu` SET `pos` = '".($menu['pos'])."' WHERE `pos` = '".($menu['pos']-1)."' LIMIT 1");
mysql_query("UPDATE `menu` SET `pos` = '".($menu['pos']-1)."' WHERE `id` = '".intval($_GET['id'])."' LIMIT 1");

msg('Пункт меню сдвинут на позицию вверх');
}
if ($_GET['act']=='down')
{
mysql_query("UPDATE `menu` SET `pos` = '".($menu['pos'])."' WHERE `pos` = '".($menu['pos']+1)."' LIMIT 1");
mysql_query("UPDATE `menu` SET `pos` = '".($menu['pos']+1)."' WHERE `id` = '".intval($_GET['id'])."' LIMIT 1");

msg('Пункт меню сдвинут на позицию вниз');
}
if ($_GET['act']=='del')
{

mysql_query("DELETE FROM `menu` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1");

msg('Пункт меню удален');
}


}



err();
aut();
echo "<table class='post'>\n";

$q=mysql_query("SELECT * FROM `menu` ORDER BY `pos` ASC");
while ($post = mysql_fetch_assoc($q))
{
echo "   <tr>\n";
if (!isset($post['icon']))mysql_query('ALTER TABLE `menu` ADD `icon` VARCHAR( 32 ) NULL DEFAULT NULL');
if (!isset($post['type']))mysql_query("ALTER TABLE  `menu` ADD  `type` ENUM('link', 'razd') NOT NULL DEFAULT 'link' AFTER `id`");
echo "  <td class='p_t'>\n";
if ($post['type']=='link')echo icons($post['icon'],'code');
echo "$post[pos]) $post[name] ".($post['type']=='link'?"($post[url])":null);
echo "  </td>\n";
echo "   </tr>\n";
echo "   <tr>\n";
echo "  <td class='p_m'>\n";


if (isset($_GET['id']) && $_GET['id']==$post['id'] && isset($_GET['act']) && $_GET['act']=='edit')
{

echo "<form action=\"?id=$post[id]\" method=\"post\">";
echo "Тип: ".($post['type']=='link'?'Ссылка':'Разделитель')."<br />\n";

echo "Название:<br />\n";
echo "<input type='text' name='name' value=\"$post[name]\" /><br />\n";

if ($post['type']=='link'){
echo "Ссылка:<br />\n";
echo "<input type='text' name='url' value='$post[url]' /><br />\n";
}
else
echo "<input type='hidden' name='url' value='' />\n";


echo "Счетчик:<br />\n";
echo "<input type='text' name='counter' value='$post[counter]' /><br />\n";
if ($post['type']=='link'){
echo "Иконка:<br />\n";
echo "<select name='icon'>\n";
echo "<option value='default.png'>По умолчанию</option>\n";
for ($i=0;$i<sizeof($icon);$i++)
{
echo "<option value='$icon[$i]'".($post['icon']==$icon[$i]?" selected='selected'":null).">$icon[$i]</option>\n";
}
echo "</select><br />\n";
}
else
echo "<input type='hidden' name='icon' value='$post[icon]' />\n";

echo "<input class=\"submit\" name=\"change\" type=\"submit\" value=\"Изменить\" /><br />\n";
echo "</form>";


echo "<a href='?'>Отмена</a><br />";
}
else
{
echo "Счетчик: ".($post['counter']==null?'отсутствует':$post['counter'])."<br />\n";

echo "<a href='?id=$post[id]&amp;act=up&amp;$passgen'>Выше</a> | ";
echo "<a href='?id=$post[id]&amp;act=down&amp;$passgen'>Ниже</a> | ";
echo "<a href='?id=$post[id]&amp;act=del&amp;$passgen'>Удалить </a><br />";

echo "<a href='?id=$post[id]&amp;act=edit&amp;$passgen'>Редактировать </a><br />";
}

echo "  </td>\n";
echo "   </tr>\n";
}


echo "</table>\n";


if (isset($_GET['add'])){
echo "<form action='?add=$passgen' method=\"post\">";
echo "Тип:<br />\n";
echo "<select name='type'>\n";
echo "<option value='link'>Ссылка (1)</option>\n";
echo "<option value='razd'>Раздел (2)</option>\n";
echo "</select><br />\n";
echo "Название (1,2):<br />\n";
echo "<input type=\"text\" name=\"name\" value=\"\"/><br />\n";
echo "Ссылка(1):<br />\n";
echo "<input type=\"text\" name=\"url\" value=\"\"/><br />\n";
echo "Счетчик (1,2):<br />\n";
echo "<input type=\"text\" name=\"counter\" value=\"\"/><br />\n";
echo "Иконка (1):<br />\n";
echo "<select name='icon'>\n";
echo "<option value='default.png'>По умолчанию</option>\n";
for ($i=0;$i<sizeof($icon);$i++)
{
echo "<option value='$icon[$i]'>$icon[$i]</option>\n";
}
echo "</select><br />\n";
echo "<input class='submit' name='add' type='submit' value='Добавить' /><br />\n";
echo "<a href='?$passgen'>Отмена</a><br />\n";
echo "</form>";
}
else echo "<div class='foot'><a href='?add=$passgen'>Добавить пункт</a></div>\n";

if (user_access('adm_panel_show')){
echo "<div class='foot'>\n";
echo "&laquo;<a href='/adm_panel/'>В админку</a><br />\n";
echo "</div>\n";
}

include_once '../sys/inc/tfoot.php';
?>
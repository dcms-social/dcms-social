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
user_access('adm_ref',null,'index.php?'.SID);
adm_check();
$set['title']='Рефералы'; // заголовок страницы
include_once '../sys/inc/thead.php';
title();
aut();


$k_post=mysql_result(mysql_query("SELECT COUNT(distinct(`url`)) FROM `user_ref`"),0);
$k_page=k_page($k_post,$set['p_str']);
$page=page($k_page);
$start=$set['p_str']*$page-$set['p_str'];
echo "<table class='post'>\n";
if ($k_post==0)
{
echo "   <tr>\n";
echo "  <td class='p_t'>\n";
echo "Нет рефералов\n";
echo "  </td>\n";
echo "   </tr>\n";

}
$q=mysql_query("SELECT COUNT(`url`) AS `count`, MAX(`time`) AS `time`, `url` FROM `user_ref` GROUP BY `url` ORDER BY `count` DESC LIMIT $start, $set[p_str]");
while ($ref = mysql_fetch_assoc($q))
{
echo "   <tr>\n";
echo "  <td class='p_t'>\n";
echo "URL: <a target='_blank' href='/go.php?go=".base64_encode("http://$ref[url]")."'>".htmlentities($ref['url'])."</a><br />\n";
echo "  </td>\n";
echo "   </tr>\n";
echo "   <tr>\n";
echo "  <td class='p_m'>\n";
echo "Переходов: $ref[count]<br />\n";
echo "Последний: ".vremja($ref['time'])."<br />\n";

echo "  </td>\n";
echo "   </tr>\n";
}
echo "</table>\n";
if ($k_page>1)str("?",$k_page,$page); // Вывод страниц

echo "<div class='foot'>\n";
if (user_access('adm_panel_show'))
echo "&laquo;<a href='/adm_panel/'>В админку</a><br />\n";
echo "</div>\n";

include_once '../sys/inc/tfoot.php';
?>
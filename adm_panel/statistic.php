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
user_access('adm_statistic',null,'index.php?'.SID);
adm_check();
$set['title']='Статистика сайта';
include_once '../sys/inc/thead.php';
title();
err();
aut();

for ($i=0;$i<24;$i++)
{
$hit=mysql_result(mysql_query("SELECT COUNT(*) FROM `visit_today` WHERE `time` >= '".mktime($i,0,0)."' AND `time` < '".mktime($i+1,0,0)."'"),0);
$host=mysql_result(mysql_query("SELECT COUNT(DISTINCT `ip`) FROM `visit_today` WHERE `time` >= '".mktime($i,0,0)."' AND `time` < '".mktime($i+1,0,0)."'"),0);
$user_reg=mysql_result(mysql_query("SELECT COUNT(*) FROM `user` WHERE `date_reg` >= '".mktime($i,0,0)."' AND `date_reg` < '".mktime($i+1,0,0)."'"),0);
$forum_them=mysql_result(mysql_query("SELECT COUNT(*) FROM `forum_t` WHERE `time_create` >= '".mktime($i,0,0)."' AND `time_create` < '".mktime($i+1,0,0)."'"),0);
$forum_post=mysql_result(mysql_query("SELECT COUNT(*) FROM `forum_p` WHERE `time` >= '".mktime($i,0,0)."' AND `time` < '".mktime($i+1,0,0)."'"),0);
$stat[]=array('hit'=>$hit,'host'=>$host,'time'=>mktime($i,0,0),'for_th'=>$forum_them,'for_p'=>$forum_post,'user'=>$user_reg);
}
echo "Текущие сутки:<br />\n";

echo "<table border='1'>";
echo "<tr>\n";
echo "<td><b>Час</b></td>\n";
echo "<td><b>Хиты</b></td>\n";
echo "<td><b>Хосты</b></td>\n";
echo "<td><b>Рег.</b></td>\n";
echo "<td><b>Форум-темы</b></td>\n";
echo "<td><b>Форум-посты</b></td>\n";
echo "</tr>\n";
for ($i=0;$i<sizeof($stat);$i++)
{
if ($time<$stat[$i]['time'])continue;
echo "<tr>\n";
echo "<td>".date('H',$stat[$i]['time']+$user['set_timesdvig']*60*60)."</td>\n";
echo "<td>".$stat[$i]['hit']."</td>\n";
echo "<td>".$stat[$i]['host']."</td>\n";
echo "<td>".$stat[$i]['user']."</td>\n";
echo "<td>".$stat[$i]['for_th']."</td>\n";
echo "<td>".$stat[$i]['for_p']."</td>\n";
echo "</tr>\n";


}
echo "</table><br />\n";
unset($stat);



echo "Последний месяц:<br />\n"; 

$k_day=mysql_result(mysql_query("SELECT COUNT(*) FROM `visit_everyday`"),0);
$q=mysql_query("SELECT * FROM `visit_everyday` ORDER BY `time` ASC LIMIT ".max($k_day-30,0).", 30");
while ($result=mysql_fetch_assoc($q)) {
$day_st=mktime(0, 0, 0, date('n', $result['time']), date('j', $result['time']));
$day_fn=mktime(0, 0, 0, date('n', $result['time']), date('j', $result['time'])+1);
$user_reg=mysql_result(mysql_query("SELECT COUNT(*) FROM `user` WHERE `date_reg` >= '$day_st' AND `date_reg` < '$day_fn'"),0);
$forum_them=mysql_result(mysql_query("SELECT COUNT(*) FROM `forum_t` WHERE `time_create` >= '$day_st' AND `time_create` < '$day_fn'"),0);
$forum_post=mysql_result(mysql_query("SELECT COUNT(*) FROM `forum_p` WHERE `time` >= '$day_st' AND `time` < '$day_fn'"),0);

$stat[]=array('host'=>($result['host_ip_ua']<$result['host']*2?$result['host_ip_ua']:$result['host']),'hit'=>$result['hit'],'time'=>$result['time'],'for_th'=>$forum_them,'for_p'=>$forum_post,'user'=>$user_reg);
}
echo "<table border='1'>";
echo "<tr>\n";
echo "<td><b>Дата</b></td>\n";
echo "<td><b>Хиты</b></td>\n";
echo "<td><b>Хосты</b></td>\n";
echo "<td><b>Рег.</b></td>\n";
echo "<td><b>Форум-темы</b></td>\n";
echo "<td><b>Форум-посты</b></td>\n";
echo "</tr>\n";
for ($i=0;$i<sizeof($stat);$i++)
{
echo "<tr>\n";
echo "<td>".date('d.m.Y',$stat[$i]['time'])."</td>\n";
echo "<td>".$stat[$i]['hit']."</td>\n";
echo "<td>".$stat[$i]['host']."</td>\n";
echo "<td>".$stat[$i]['user']."</td>\n";
echo "<td>".$stat[$i]['for_th']."</td>\n";
echo "<td>".$stat[$i]['for_p']."</td>\n";
echo "</tr>\n";
}
echo "</table><br />\n";











if (user_access('adm_panel_show')){
echo "<div class='foot'>\n";
echo "<a href='/adm_panel/'>Админка</a><br />\n";
echo "</div>\n";}
include_once '../sys/inc/tfoot.php';
?>
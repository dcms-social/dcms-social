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
only_reg();
$set['title']='Оценки';
include_once '../../sys/inc/thead.php';
title();

if (isset($user))$ank['id']=$user['id'];

$ank=get_user($ank['id']);
if(!$ank){header("Location: /index.php?".SID);exit;}

err();
aut(); // форма авторизации

echo "<div class='foot'>\n";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/info.php'>$user[nick]</a> | Оценки<br />\n";
echo "</div>\n";


$k_post=mysql_result(mysql_query("SELECT COUNT(*) FROM `gallery_rating` WHERE `avtor` = '$ank[id]'"),0);
$k_page=k_page($k_post,$set['p_str']);
$page=page($k_page);
$start=$set['p_str']*$page-$set['p_str'];
$q=mysql_query("SELECT * FROM `gallery_rating` WHERE `avtor` = '$ank[id]' ORDER BY `time` DESC LIMIT $start, $set[p_str]");

if ($k_post==0)
{
echo "  <div class='mess'>\n";
echo "Нет оценок\n";
echo "  </div>\n";
}
$num=0;
while ($post = mysql_fetch_assoc($q))
{
//$ank=mysql_fetch_assoc(mysql_query("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));
$ank2=get_user($post['id_user']);
$foto=mysql_fetch_assoc(mysql_query("SELECT * FROM `gallery_foto` WHERE `id` = $post[id_foto]"));
if ($foto['id'] && $ank2['id'])
{
$gallery=mysql_fetch_assoc(mysql_query("SELECT * FROM `gallery` WHERE `id` = $foto[id_gallery]"));
//-----------зебра-----------//
if ($num==0)
{echo "  <div class='nav1'>\n";
$num=1;
}elseif ($num==1)
{echo "  <div class='nav2'>\n";
$num=0;}
//---------------------------//
if($post['read']==1)
{
$color = "<font color='red'>";
$color2 = "</font>";
}
else
{
$color = null;
$color2 = null;
}
echo "<table>\n";
echo "   <tr>\n";

echo "  <td style='vertical-align:top;'>\n";
status($ank2['id']) . group($ank2['id']);
echo "<a href='/info.php?id=$ank2[id]'>$ank2[nick]</a> ".medal($ank2['id'])." ".online($ank2['id'])."<br />\n";
echo "<img src='/style/icons/$post[like].png' alt=''/> $color".vremja($post['time'])."$color2";
echo "  </td>\n";

echo "  <td style='vertical-align:top;'>\n";
echo "<a href='/foto/$user[id]/$gallery[id]/$foto[id]/'><img class='show_foto' src='/foto/foto" . ($set['web'] ? "128" : "50") . "/$foto[id].$foto[ras]' alt='$foto[name]' align='right'/></a>\n";
echo "  </td>\n";

echo "   </tr>\n";
echo "</table>\n";
echo "</div>";
}else{
mysql_query("DELETE FROM `gallery_rating` WHERE `avtor` = '$post[avtor]' AND `id_foto` = '$post[id_foto]'");
}
}

mysql_query("UPDATE `gallery_rating` SET `read`='0' WHERE `avtor` = '$user[id]' AND `read`='1'");

if ($k_page>1)str("?",$k_page,$page); // Вывод страниц

echo "<div class='foot'>\n";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/info.php'>$user[nick]</a> | Оценки<br />\n";
echo "</div>\n";

include_once '../../sys/inc/tfoot.php';
?>
<?
err();
aut();

$k_post=mysql_result(mysql_query("SELECT COUNT(*) FROM `forum_r` WHERE `id_forum` = '$forum[id]'"),0);
$k_page=k_page($k_post,$set['p_str']);
$page=page($k_page);
$start=$set['p_str']*$page-$set['p_str'];

echo "<table class='post'>\n";

$q=mysql_query("SELECT * FROM `forum_r` WHERE `id_forum` = '$forum[id]' ORDER BY `time` DESC LIMIT $start, $set[p_str]");

if (mysql_num_rows($q)==0) {
	echo "  <div class='mess'>\n";
	echo "Нет разделов\n";
	echo "  </div>\n";
}	

while ($razdel = mysql_fetch_assoc($q))
{
/*-----------зебра-----------*/	
if ($num==0)	
{		
echo "  <div class='nav1'>\n";
$num=1;	
}	
elseif ($num==1)
{	
echo "  <div class='nav2'>\n";	
$num=0;	
}	
/*---------------------------*/

echo "<a href='/forum/$forum[id]/$razdel[id]/'>" . text($razdel['name']) . "</a> [".mysql_result(mysql_query("SELECT COUNT(*) FROM `forum_p` WHERE `id_forum` = '$forum[id]' AND `id_razdel` = '$razdel[id]'"),0).'/'.mysql_result(mysql_query("SELECT COUNT(*) FROM `forum_t` WHERE `id_forum` = '$forum[id]' AND `id_razdel` = '$razdel[id]'"),0)."]\n";
if(!empty($razdel['opis'])){ echo '<br/><span style="color:#666;">'.output_text($razdel['opis']).'</span>'; }
echo "   </div>\n";
}
echo "</table>\n";
if ($k_page>1)str("/forum/$forum[id]/?",$k_page,$page); // Вывод страниц
?>
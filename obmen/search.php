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
if (mysql_result(mysql_query("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'files' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0)!=0)
{
header('Location: /ban.php?'.SID);exit;
}

$set['title']='Поиск файлов'; // заголовок страницы
include_once '../sys/inc/thead.php';
title();
aut();
echo "<div class='foot'>";echo '<img src="/style/icons/up_dir.gif" alt="*"> <a href="/obmen/">Обменник</a><br />';echo "</div>\n";
$search=NULL;
if (isset($_SESSION['search']))$search=$_SESSION['search'];
if (isset($_POST['search']))$search=$_POST['search'];
$_SESSION['search']=$search;

$search=preg_replace("#( ){2,}#"," ",$search);
$search=preg_replace("#^( ){1,}|( ){1,}$#","",$search);



if (isset($_GET['go']) && $search!=NULL)
{
$search_a=explode(' ', $search);

for($i=0;$i<count($search_a);$i++)
{
$search_a2[$i]='<span class="search_c">'.stripcslashes(htmlspecialchars($search_a[$i])).'</span>';
$search_a[$i]=stripcslashes(htmlspecialchars($search_a[$i]));
}

$q_search=str_replace('%','',$search);
$q_search=str_replace(' ','%',$q_search);
$k_post=mysql_result(mysql_query("SELECT COUNT(*) FROM `obmennik_files` WHERE `opis` like '%".mysql_escape_string($q_search)."%' OR `name` like '%".mysql_escape_string($q_search)."%'"),0);
$k_page=k_page($k_post,$set['p_str']);
$page=page($k_page);
$start=$set['p_str']*$page-$set['p_str'];
if ($k_post==0)echo "<div class=\"p_t\">\nНет результатов</div>\n";
$q=mysql_query("SELECT * FROM `obmennik_files` WHERE `opis` like '%".mysql_escape_string($q_search)."%' OR `name` like '%".mysql_escape_string($q_search)."%' ORDER BY `time` DESC LIMIT $start, $set[p_str]");
$i=0;
while ($post = mysql_fetch_assoc($q))
{$k_p=mysql_result(mysql_query("SELECT COUNT(*) FROM `obmennik_komm` WHERE `id_file` = '$post[id]'"),0);
$ras=$post['ras'];
$file=H."sys/obmen/files/$post[id].dat";
$name=$post['name'];
$size=$post['size'];
$dir_id = mysql_fetch_array(mysql_query("SELECT * FROM `obmennik_dir` WHERE `id` = '$post[id_dir]' LIMIT 1"));
/*-----------зебра-----------*/ if ($num==0){echo '<div class="nav1">';$num=1;}elseif ($num==1){echo '<div class="nav2">';$num=0;}/*---------------------------*/include 'inc/icon48.php';if (is_file(H.'style/themes/'.$set['set_them'].'/loads/14/'.$ras.'.png'))echo "<img src='/style/themes/$set[set_them]/loads/14/$ras.png' alt='$ras' /> \n";else echo "<img src='/style/themes/$set[set_them]/loads/14/file.png' alt='file' /> \n";if ($set['echo_rassh']==1)$ras=$post['ras'];else $ras=NULL;echo '<a href="/obmen'.$dir_id['dir'] . $post['id'].'.'.$post['ras'].'?showinfo"><b>'.$post['name'].'.'.$ras.'</b></a> ('.size_file($post['size']).')<br />';if ($post['opis'])echo rez_text(htmlspecialchars($post['opis'])).'<br />';echo '<a href="/obmen'.$dir_id['dir'] . $post['id'].'.'.$post['ras'].'?showinfo&amp;komm">Комментарии</a> ('.$k_p.')<br />';echo '</div>';
}
if ($k_page>1){str("search.php?go&amp;",$k_page,$page);
echo '<br />';} // Вывод страниц
}
else
echo '<div class="foot">';
echo 'Поиск файлов';
echo '</div>';
echo "<form method=\"post\" action=\"search.php?go\" class=\"search\">\n";
$search=stripcslashes(htmlspecialchars($search));
echo "<input type=\"text\" name=\"search\" maxlength=\"64\" value=\"$search\" /><br />\n";
echo "<input type=\"submit\" value=\"Поиск\" />\n";
echo "</form>\n";

echo "<div class='foot'>";
echo '<img src="/style/icons/up_dir.gif" alt="*"> <a href="/obmen/">Обменник</a><br />';
echo "</div>\n";

include_once '../sys/inc/tfoot.php';
?>
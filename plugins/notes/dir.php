<?
/*
=======================================
Дневники для Dcms-Social
Автор: Искатель
---------------------------------------
Этот скрипт распостроняется по лицензии
движка Dcms-Social. 
При использовании указывать ссылку на
оф. сайт http://dcms-social.ru
---------------------------------------
Контакты
ICQ: 587863132
http://dcms-social.ru
=======================================
*/
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/adm_check.php';
include_once '../../sys/inc/user.php';
/* Бан пользователя */ 
if (isset($user) && mysql_result(mysql_query("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'notes' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0)!=0)
{
header('Location: /ban.php?'.SID);exit;
}
$set['title']='Категории';
include_once '../../sys/inc/thead.php';
title();


if (isset($_POST['title']) && user_access('notes_edit'))
{
$title=my_esc($_POST['title'],1);
$msg=my_esc($_POST['msg']);


if (strlen2($title)>32){$err='Название не может превышать больше 32 символов';}
if (strlen2($title)<3){$err='Короткое название';}

if (strlen2($msg)>10024){$err='Содержание не может превышать больше 10024 символов';}
if (strlen2($msg)<2){$err='Содержание слишком короткое';}

if (!isset($err)){
mysql_query("INSERT INTO `notes_dir` (`msg`, `name`) values('$msg', '$title')");
mysql_query("OPTIMIZE TABLE `notes_dir`");

$_SESSION['message']='Категория успешно создана';
header("Location: dir.php?".SID);
exit;
}
}

err();
aut();
echo "<div id='comments' class='menus'>";

echo "<div class='webmenu'>";
echo "<a href='index.php'>Дневники</a>";
echo "</div>"; 

        
echo "<div class='webmenu last'>";
echo "<a href='dir.php' class='activ'>Категории</a>";
echo "</div>"; 
        
echo "<div class='webmenu last'>";
echo "<a href='search.php'>Поиск</a>";
echo "</div>"; 

echo "</div>";

/*
==================================
Дневники
==================================
*/

if (isset($_GET['id']))
{
$id_dir=intval($_GET['id']);
$kount=mysql_result(mysql_query("SELECT COUNT(*) FROM `notes_dir` WHERE `id` = '$id_dir' "),0);
}
if (isset($_GET['id']) && $kount==1)
{
if (isset($_GET['sort']) && $_GET['sort'] =='t')$order='order by `time` desc';
elseif (isset($_GET['sort']) && $_GET['sort'] =='c') $order='order by `count` desc';
else $order='order by `time` desc';
if(isset($user))
{
echo'<div class="foot">';
echo "<a href=\"user.php\">Мои дневники</a> | ";
echo "<a href=\"add.php?id_dir=$id_dir\">Создать дневник</a>";
echo '</div>';
}
if (isset($_GET['sort']) && $_GET['sort'] =='t'){
echo'<div class="foot">';
echo"<b>Новые</b> | <a href='?id=$id_dir&amp;sort=c'>Популярные</a>\n";
echo '</div>';
}elseif (isset($_GET['sort']) && $_GET['sort'] =='c'){
echo'<div class="foot">';
echo"<a href='?id=$id_dir&amp;sort=t'>Новые</a> | <b>Популярные</b>\n";
echo '</div>';
}else{
echo'<div class="foot">';
echo"<b>Новые</b> | <a href='?id=$id_dir&amp;sort=c'>Популярные</a>\n";
echo '</div>';
}
$k_post=mysql_result(mysql_query("SELECT COUNT(*) FROM `notes`  WHERE `id_dir` = '$id_dir'"),0);
$k_page=k_page($k_post,$set['p_str']);
$page=page($k_page);
$start=$set['p_str']*$page-$set['p_str'];
$q=mysql_query("SELECT * FROM `notes` WHERE `id_dir` = '$id_dir' $order LIMIT $start, $set[p_str]");

if ($k_post==0)
{

echo "  <div class='mess'>\n";
echo "Нет записей\n";
echo "  </div>\n";

}
$num=0;
while ($post = mysql_fetch_assoc($q))
{
/*-----------зебра-----------*/
if ($num==0)
{echo "  <div class='nav1'>\n";
$num=1;
}elseif ($num==1)
{echo "  <div class='nav2'>\n";
$num=0;}
/*---------------------------*/


echo "<img src='/style/icons/dnev.png' alt='*'> ";

echo "<a href='list.php?id=$post[id]&amp;dir=$post[id_dir]'>" . htmlspecialchars($post['name']) . "</a> \n";

echo " <span style='time'>(".vremja($post['time']).")</span>\n";

$k_n= mysql_result(mysql_query("SELECT COUNT(*) FROM `notes` WHERE `id` = $post[id] AND `time` > '".$ftime."'",$db), 0);
if ($k_n!=0)echo " <img src='/style/icons/new.gif' alt='*'>";


echo "   </div>\n";
}

if (isset($_GET['sort'])) $dop="sort=" . my_esc($_GET['sort']) . "&amp;";
else $dop='';
if ($k_page>1)str('?id='.$id_dir.'&'.$dop.'',$k_page,$page); // Вывод страниц

include_once '../../sys/inc/tfoot.php';
exit;
}


/*
==================================
Категории
==================================
*/
$k_post=mysql_result(mysql_query("SELECT COUNT(*) FROM `notes_dir` "),0);
$q=mysql_query("SELECT * FROM `notes_dir` ORDER BY `id` DESC");
echo "<table class='post'>\n";
if ($k_post==0)
{
echo "  <div class='mess'>\n";
echo "Нет категорий\n";
echo "  </div>\n";
}
$num=0;
while ($post = mysql_fetch_assoc($q))
{
/*-----------зебра-----------*/
if ($num==0)
{echo "  <div class='nav1'>\n";
$num=1;
}elseif ($num==1)
{echo "  <div class='nav2'>\n";
$num=0;}
/*---------------------------*/

echo "<img src='/style/themes/$set[set_them]/loads/14/dir.png' alt='*'> ";
$k_pp=mysql_result(mysql_query("SELECT COUNT(*) FROM `notes`  WHERE `id_dir` = '$post[id]'"),0);
$k_nn=mysql_result(mysql_query("SELECT COUNT(*) FROM `notes`  WHERE `id_dir` = '$post[id]' AND `time` > '$ftime'"),0);
if ($k_nn>0)
$k_nn="<font color='red'>+$k_nn</font>";
else
$k_nn=NULL;

echo "<a href='dir.php?id=$post[id]'>" . output_text($post['name']) . "</a> ($k_pp) $k_nn\n";


if (isset($user) && ($user['level']>3))
echo "<a href='delete.php?dir=$post[id]'><img src='/style/icons/delete.gif' alt='*'></a><br />\n";
//$k_n= mysql_result(mysql_query("SELECT COUNT(*) FROM `notes` WHERE `id_dir` = $post[id] AND `time` > '".$ftime."'",$db), 0);

echo output_text($post['msg'])."<br />\n";

echo "   </div>\n";
}
echo "</table>\n";


if (isset($user) && user_access('notes_edit')){
if (isset($_GET['create'])){
echo "<form method=\"post\" action=\"dir.php\">\n";
echo "Название:<br />\n<input name=\"title\" size=\"16\" maxlength=\"32\" value=\"\" type=\"text\" /><br />\n";
echo "Описание:<br />\n<textarea name=\"msg\" ></textarea><br />\n";

echo "<input value=\"Создать\" type=\"submit\" />\n";
echo "</form>\n";
}else{
echo "<div class='foot'>\n";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='dir.php?create'>Добавить категорию</a><br />\n";
echo "</div>\n";
}
}

echo "<div class='foot'>\n";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='index.php'>Все дневники</a><br />\n";
echo "</div>\n";

include_once '../../sys/inc/tfoot.php';
?>
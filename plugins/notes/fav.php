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

/* Бан пользователя */ 
if (isset($user) && mysql_result(mysql_query("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'notes' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0')"), 0)!=0)
{
	header('Location: /ban.php?'.SID);
	exit;
}
$set['title']='Добавили в закладки';
include_once '../../sys/inc/thead.php';
title();
aut();
if(mysql_result(mysql_query("SELECT COUNT(*)FROM `notes` WHERE `id`='".intval($_GET['id'])."' LIMIT 1"),0)==0){
echo "<div class='err'>Легче, легче! Такого дневника нет.</div>";
include_once '../../sys/inc/tfoot.php';
exit;
}else{
$k_post=mysql_result(mysql_query("SELECT COUNT(*)FROM `bookmarks` WHERE `id_object`='".intval($_GET['id'])."' AND `type`='notes' "),0);
$k_page=k_page($k_post,$set['p_str']);


	$page=page($k_page);


	$start=$set['p_str']*$page-$set['p_str'];
if($k_post==0){
echo "<div class='mess'>Никто в закладки не добавлял</div>";
}else{
$q=mysql_query("SELECT*FROM `bookmarks` WHERE `id_object`='".intval($_GET['id'])."' AND `type`='notes' LIMIT $start,$set[p_str]");
while($post=mysql_fetch_assoc($q)){
echo "<div class='nav2'>";
echo group($post['id_user'])." ";
echo user::nick($post['id_user'],1,1,1)." ";
echo "Добавлено ".vremja($post['time'])."</div>";
}
if ($k_page > 1)str("?id=".intval($_GET['id'])."&amp;",$k_page,$page); // Вывод страниц
}
include_once '../../sys/inc/tfoot.php';

}
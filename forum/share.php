<?php
/*
=======================================
Модуль "Поделиться темой форума" от PluginS
=======================================
*/
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';

if (isset($user) && mysql_result(mysql_query("SELECT COUNT(`id`) FROM `ban` WHERE `razdel` = 'forum' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0)!=0)
{
header('Location: /ban.php?'.SID);exit;
}

$set['title']='Share Them';
include_once '../sys/inc/thead.php';
title();
aut();
$not=mysql_query("SELECT * FROM `forum_t` WHERE `id`='".intval($_GET['id'])."' LIMIT 1");
if(mysql_num_rows($not)==0){
echo "<div class='error'>Такой темы не существует</div>";
include_once '../sys/inc/tfoot.php';
exit;
}
if(mysql_result(mysql_query("SELECT COUNT(`id`)FROM `notes` WHERE `id_user`='".$user['id']."' AND `share_id`='".intval($_GET['id'])."' AND `share_type`='forum' LIMIT 1"),0)==1){
echo "<div class='error'>Вы уже поделились данной темой</div>";
include_once '../sys/inc/tfoot.php';
exit;
}else{
$notes=mysql_fetch_assoc($not);
$avtor=get_user($notes['id_user']);
if(isset($_POST['ok'])){
mysql_query("INSERT INTO `notes`(`id_user`,`name`,`msg`,`share`,`share_text`,`share_id`,`share_id_user`,`share_name`,`time`,`share_type`) values('".$user['id']."','".text($notes['name'])."','".my_esc($_POST['share_text'])."','1','".my_esc($notes['text'])."','".$notes['id']."','".$notes['id_user']."','".my_esc($notes['name'])."','".$time."','forum')");
$id=mysql_insert_id();
msg('Ок всё крч');
header('Location:/plugins/notes/list.php?id='.$id);
exit;
}

?>
<div class='nav2'><div class="friends_access_list attach_block mt_0 grey"> <? echo group($avtor['id'])." ";?> <a href="/info.php?id=<?=$notes['id_user']?>"><span style="color:#79358c"><b><? echo " ".$avtor['nick']." ";?> </b></span></a> : <? echo '<a href="/forum/'.$notes['id_forum'].'/'.$notes['id_razdel'].'/'.$notes['id'].'/">';?>
<span style="color:#06F;"><? echo $notes['name']; ?></span></a></div>
<?
echo "<form method='post' action='share.php?id=".intval($_GET['id'])."'>";
echo $tPanel;
echo "<textarea name='share_text'></textarea>";
echo "<br/><input type='submit' name='ok' value='Поделиться'>";
echo "</form></div>";
}
include_once '../sys/inc/tfoot.php';
?>
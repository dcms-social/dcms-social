<?php
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/user.php';
/* Бан недоюзера */
if(mysql_result(mysql_query("SELECT COUNT(`id`)FROM `user` WHERE `id`='".intval($_GET['id'])."' LIMIT 1"),0)==0){
header('Location: /index.php');
exit;
}
if(isset($_GET['id']))$ank=get_user(intval($_GET['id']));
else $ank=get_user($user['id']);
$set['title']='Темы и комментарии '.$ank['nick'];
include_once '../../sys/inc/thead.php';
title();
aut();
echo "<div class='nav1'>Автор: ";
echo group($ank['id']);
echo " ".user::nick($ank['id'],1,1,1)."</div>";
/* Sort (Thems OR Komments) */
echo "<div class='nav1'>";
if(isset($_GET['komm']))echo "<a href='?id=".$ank['id']."'>Темы</a> | <b>Комментарии</b>";
else echo "<b>Темы</b> | <a href='?id=".$ank['id']."&komm'>Комментарии</a>";
echo "</div>";

//Если коммы смотрим
if(isset($_GET['komm'])){
$k_post = mysql_result(mysql_query("SELECT COUNT(`id`) FROM `forum_p` WHERE `id_user`='".$ank['id']."'"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
$q=mysql_query("SELECT id_them, msg,id, id_razdel, id_forum,id_them FROM `forum_p` WHERE `id_user`='".$ank['id']."' ORDER BY `time` DESC LIMIT $start,$set[p_str]");
while($post=mysql_fetch_assoc($q)){
echo "<div class='nav1'><a href='/forum/".$post['id_forum']."/".$post['id_razdel']."/".$post['id_them']."/'>";
echo rez_text($post['msg'],80)." ...";
echo "</a></div>";
}
if ($k_page > 1)str('them.php?id='.$ank['id'].'&komm&amp;', $k_page, $page); // Вывод страниц
}else{
//Если темы смотрим
$k_post = mysql_result(mysql_query("SELECT COUNT(`id`) FROM `forum_t` WHERE `id_user`='".$ank['id']."'"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
$q=mysql_query("SELECT id, name, id_forum, id_razdel FROM `forum_t` WHERE `id_user`='".$ank['id']."' ORDER BY `time` DESC LIMIT $start,$set[p_str]");
while($them=mysql_fetch_assoc($q)){
echo "<div class='nav1'><a href='/forum/".$them['id_forum']."/".$them['id_razdel']."/".$them['id']."/'>";
echo htmlspecialchars($them['name'])." </a> (".mysql_result(mysql_query("SELECT COUNT(*)FROM `forum_p` WHERE `id_them`='".$them['id']."'"),0).")";
echo "</div>";
}
if ($k_page > 1)str('them.php?id='.$ank['id'].'&', $k_page, $page); // Вывод страниц
}
//Конец, ёптить
include_once '../../sys/inc/tfoot.php';
?>






?>
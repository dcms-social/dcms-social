<?php
/*
=======================================
Музыка юзеров для Dcms-Social
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
include_once '../../sys/inc/user.php';
include_once '../../sys/inc/files.php';

/* Бан пользователя */ 
if (mysql_result(mysql_query("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'files' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0)!=0)
{
header('Location: /ban.php?'.SID);exit;
}

include_once '../../sys/inc/thead.php';

if (isset($user))$ank['id']=$user['id'];
if (isset($_GET['id']))$ank['id']=intval($_GET['id']);

if ($ank['id']==0)
{
echo "Ошибка! Это музыка системы, здесь нет треков =)";
exit;
}
 // Определяем id автора плейлиста
$ank=get_user($ank['id']);
if(!$ank){header("Location: /index.php?".SID);exit;}

$set['title'] = 'Музыка '.$ank['nick'];
title();
aut();
?>
<style>
#ajaxsPlayer{
margin:auto;
}
.button{
float:left;
}
.play{
width:20px;
height:20px;
background-image:url(/style/icons/play.png);
display:block;
cursor:pointer;
margin:2px;
}
.pause{
width:20px;
height:20px;
background-image:url(/style/icons/pause.png);
display:block;
cursor:pointer;
display:none;
margin:2px;
}

.nameTrack{
font: 14px/90% Helvetica, 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
color: #666666;
padding:5px 30px;
vertical-align:middle;
width:90%;
}
.clear{
	clear:both;	
}
</style>
<script type="text/javascript" src="/ajax/js/jquery-1.5.1.min.js"></script>
<script type="text/javascript" src="/ajax/js/user-music.js"></script>
<div id="ajaxsPlayer">
<?
echo "<div class=\"foot\">\n";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/info.php?id=$ank[id]'>$ank[nick]</a> | \n";
echo '<b>Музыка</b>';
echo "</div>\n";


if ($set['web'])$set['p_str'] = 100;
$k_post=mysql_result(mysql_query("SELECT COUNT(*) FROM `user_music` WHERE `id_user` = '$ank[id]'"),0);
$k_page=k_page($k_post,$set['p_str']);
$page=page($k_page);
$start=$set['p_str']*$page-$set['p_str'];

if ($k_post==0)
{
echo "<div class='mess'>";
echo "Нет треков в плейлисте\n";
echo '</div>';
}
$track = 0;
$q=mysql_query("SELECT * FROM `user_music` WHERE `id_user` = '$ank[id]' ORDER BY `id` DESC LIMIT $start, $set[p_str]");
while ($post = mysql_fetch_assoc($q))
{
$mp3 = mysql_fetch_assoc(mysql_query("SELECT * FROM `obmennik_files` WHERE `id` = '$post[id_file]' LIMIT 1"));
$dir = mysql_fetch_assoc(mysql_query("SELECT * FROM `obmennik_dir` WHERE `id` = '$mp3[id_dir]' LIMIT 1"));
$ras = $mp3['ras'];

/*-----------зебра-----------*/
if ($num==0)
{echo "  <div class='nav1'>\n";
$num=1;
}elseif ($num==1)
{echo "  <div class='nav2'>\n";
$num=0;}
/*---------------------------*/
if ($webbrowser=='web')
{
	echo '<div class="track">';
		echo '<div class="button">';
			echo '<div class="play" id="'.$track.'" file="/obmen'.$dir['dir'].'/'.$mp3['id'].'.'.$ras.'"></div>';
			echo '<div class="pause"></div>';	
		echo '</div>';
	echo '<div class="nameTrack"><a href="/obmen'.$dir['dir'] . $mp3['id'].'.'.$ras.'">
	<img src="/style/icons/d.gif" alt="*" title="Скачать трек"></a> ' . htmlspecialchars($mp3['name']) . ' (' . size_file($mp3['size']) . ')</div>
	<div class="clear"></div>';
	echo '</div>';
}else{
	echo '<a href="/obmen'.$dir['dir'] . $mp3['id'].'.'.$ras.'">
	<img src="/style/icons/d.gif" alt="*" title="Скачать трек"></a> ' . htmlspecialchars($mp3['name']) . ' (' . size_file($mp3['size']) . ')';
}
echo '</div>';
$track++;
}
?>
</div>
<?
if ($k_page>1)str('index.php?id='.$ank['id'].'&amp;',$k_page,$page); // Вывод страниц

echo "<div class=\"foot\">\n";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/info.php?id=$ank[id]'>$ank[nick]</a> | \n";
echo '<b>Музыка</b>';
echo "</div>\n";

 // (c) Искатель
include_once '../../sys/inc/tfoot.php';

?>
<?
/*
=======================================
Личные файлы юзеров для Dcms-Social
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


$file_id=mysql_fetch_assoc(mysql_query("SELECT * FROM `obmennik_files` WHERE `id`='".intval($_GET['id_file'])."' LIMIT 1"));

if ($file_id['id_user']!=$ank['id']){echo 'Ошибка!';exit;}

$dir_id=mysql_fetch_assoc(mysql_query("SELECT * FROM `obmennik_dir` WHERE `id` = '$file_id[id_dir]' LIMIT 1"));
$ras=$file_id['ras'];
$file=H."sys/obmen/files/$file_id[id].dat";
$name=$file_id['name'];
$size=$file_id['size'];



/*
================================
Модуль жалобы на пользователя
и его сообщение либо контент
в зависимости от раздела
================================
*/
if (isset($_GET['spam'])  && isset($user))
{
$mess = mysql_fetch_assoc(mysql_query("SELECT * FROM `obmennik_komm` WHERE `id` = '".intval($_GET['spam'])."' limit 1"));
$spamer = get_user($mess['id_user']);
if (mysql_result(mysql_query("SELECT COUNT(*) FROM `spamus` WHERE `id_user` = '$user[id]' AND `id_spam` = '$spamer[id]' AND `razdel` = 'files_komm' AND `spam` = '".$mess['msg']."'"),0)==0)
{
if (isset($_POST['msg']))
{
if ($mess['id_user']!=$user['id'])
{
$msg=mysql_real_escape_string($_POST['msg']);

if (strlen2($msg)<3)$err='Укажите подробнее причину жалобы';
if (strlen2($msg)>1512)$err='Длина текста превышает предел в 512 символов';

if(isset($_POST['types'])) $types=intval($_POST['types']);
else $types='0'; 
if (!isset($err))
{
mysql_query("INSERT INTO `spamus` (`id_object`, `id_user`, `msg`, `id_spam`, `time`, `types`, `razdel`, `spam`) values('$file_id[id]', '$user[id]', '$msg', '$spamer[id]', '$time', '$types', 'files_komm', '".my_esc($mess['msg'])."')");
$_SESSION['message'] = 'Заявка на рассмотрение отправлена'; 
header("Location: ?id_file=$file_id[id]&spam=$mess[id]&page=".intval($_GET['page'])."");
exit;
}
}
}
}
$set['title']= 'Жалоба'; // заголовок страницы
include_once '../../sys/inc/thead.php';
title();
aut();
err();

if (mysql_result(mysql_query("SELECT COUNT(*) FROM `spamus` WHERE `id_user` = '$user[id]' AND `id_spam` = '$spamer[id]' AND `razdel` = 'files_komm'"),0)==0)
{
echo "<div class='mess'>Ложная информация может привести к блокировке ника. 
Если вас постоянно достает один человек - пишет всякие гадости, вы можете добавить его в черный список.</div>";
echo "<form class='nav1' method='post' action='?id_file=$file_id[id]&amp;spam=$mess[id]&amp;page=".intval($_GET['page'])."'>\n";
echo "<b>Пользователь:</b> ";
echo " ".status($spamer['id'])."  ".group($spamer['id'])." <a href=\"/info.php?id=$spamer[id]\">$spamer[nick]</a>\n";
echo "".medal($spamer['id'])." ".online($spamer['id'])." (".vremja($mess['time']).")<br />";
echo "<b>Нарушение:</b> <font color='green'>".output_text($mess['msg'])."</font><br />";
echo "Причина:<br />\n<select name='types'>\n";
echo "<option value='1' selected='selected'>Спам/Реклама</option>\n";
echo "<option value='2' selected='selected'>Мошенничество</option>\n";
echo "<option value='3' selected='selected'>Оскорбление</option>\n";
echo "<option value='0' selected='selected'>Другое</option>\n";
echo "</select><br />\n";
echo "Комментарий:$tPanel";
echo "<textarea name=\"msg\"></textarea><br />";
echo "<input value=\"Отправить\" type=\"submit\" />\n";
echo "</form>\n";
}else{
echo "<div class='mess'>Жалоба на <font color='green'>$spamer[nick]</font> будет рассмотрена в ближайшее время.</div>";
}

echo "<div class='foot'>\n";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='?id_file=$file_id[id]&amp;page=".intval($_GET['page'])."'>Назад</a><br />\n";
echo "</div>\n";
include_once '../../sys/inc/tfoot.php';
exit;
}
/*
==================================
The End
==================================
*/

/*------------очищаем счетчик этого обсуждения-------------*/
if (isset($user))
{
mysql_query("UPDATE `discussions` SET `count` = '0' WHERE `id_user` = '$user[id]' AND `type` = 'obmen' AND `id_sim` = '$file_id[id]' LIMIT 1");
}
/*---------------------------------------------------------*/


/*------------------------Мне нравится------------------------*/
if (isset($user) && $ank['id']!=$user['id'] && isset($_GET['like']) && ($_GET['like']==1 || $_GET['like']==0) && mysql_result(mysql_query("SELECT COUNT(*) FROM `like_object` WHERE `id_object` = '$file_id[id]' AND `type` = 'obmen' AND `id_user` = '$user[id]'"),0)==0)
{
mysql_query("INSERT INTO `like_object` (`id_user`, `id_object`, `type`, `like`) VALUES ('$user[id]', '$file_id[id]', 'obmen', '".intval($_GET['like'])."')");
mysql_query("UPDATE `user` SET `balls` = '".($ank['balls']+1)."', `rating_tmp` = '".($ank['rating_tmp']+1)."' WHERE `id` = '$ank[id]' LIMIT 1");

}
/*------------------------------------------------------------*/



/*------------------------Моя музыка--------------------------*/
$music_people = mysql_result(mysql_query("SELECT COUNT(*) FROM `user_music` WHERE `dir` = 'obmen' AND `id_file` = '$file_id[id]'"),0);
if (isset($user))
$music = mysql_result(mysql_query("SELECT COUNT(*) FROM `user_music` WHERE `id_user` = '$user[id]' AND `dir` = 'obmen' AND `id_file` = '$file_id[id]'"),0);

if (isset($user) && isset($_GET['play']) && ($_GET['play']==1 || $_GET['play']==0) && ($file_id['ras']=='mp3' || $file_id['ras']=='wav' || $file_id['ras']=='ogg'))
{
	if ($_GET['play']==1 && $music==0) // Добавляем в плейлист
	{
	mysql_query("INSERT INTO `user_music` (`id_user`, `id_file`, `dir`) VALUES ('$user[id]', '$file_id[id]', 'obmen')");
	mysql_query("UPDATE `user` SET `balls` = '".($ank['balls']+1)."', `rating_tmp` = '".($ank['rating_tmp']+1)."' WHERE `id` = '$ank[id]' LIMIT 1");
	$_SESSION['message']='Трек добавлен в плейлист';
	}
	
	if ($_GET['play']==0 && $music==1) // Удаляем из плейлиста
	{
	mysql_query("DELETE FROM `user_music` WHERE `id_user` = '$user[id]' AND `id_file` = '$file_id[id]' AND `dir` = 'obmen' LIMIT 1");
	mysql_query("UPDATE `user` SET `rating_tmp` = '".($ank['rating_tmp']-1)."' WHERE `id` = '$ank[id]' LIMIT 1");
	$_SESSION['message']='Трек удален из плейлиста';
	}
	header ("Location: ?id_file=$file_id[id]");
	exit;
}
/*------------------------------------------------------------*/
 
$set['title']= htmlspecialchars($file_id['name']); // заголовок страницы
include_once '../../sys/inc/thead.php';
title();

if ((user_access('obmen_komm_del') || $ank['id'] == $user['id']) && isset($_GET['del_post']) && mysql_result(mysql_query("SELECT COUNT(*) FROM `obmennik_komm` WHERE `id` = '".intval($_GET['del_post'])."' AND `id_file` = '$file_id[id]'"),0))
{
mysql_query("DELETE FROM `obmennik_komm` WHERE `id` = '".intval($_GET['del_post'])."' LIMIT 1");
$_SESSION['message']='Комментарий успешно удален';
header ("Location: ?id_file=$file_id[id]");
}

if (isset($user))
mysql_query("UPDATE `notification` SET `read` = '1' WHERE `type` = 'files_komm' AND `id_user` = '$user[id]' AND `id_object` = '$file_id[id]'");

if (isset($_POST['msg']) && isset($user))
{
$msg=$_POST['msg'];
if (isset($_POST['translit']) && $_POST['translit']==1)$msg=translit($msg);

$mat=antimat($msg);
if ($mat)$err[]='В тексте сообщения обнаружен мат: '.$mat;

if (strlen2($msg)>1024){$err[]='Сообщение слишком длинное';}
elseif (strlen2($msg)<2){$err[]='Короткое сообщение';}
elseif (mysql_result(mysql_query("SELECT COUNT(*) FROM `obmennik_komm` WHERE `id_file` = '$file_id[id]' AND `id_user` = '$user[id]' AND `msg` = '".mysql_escape_string($msg)."' LIMIT 1"),0)!=0){$err='Ваше сообщение повторяет предыдущее';}
elseif(!isset($err)){
$ank=get_user($file_id['id_user']);

/*
====================================
Обсуждения
====================================
*/
$q = mysql_query("SELECT * FROM `frends` WHERE `user` = '".$file_id['id_user']."' AND `i` = '1'");
while ($f = mysql_fetch_array($q))
{
$a=get_user($f['frend']);
$discSet = mysql_fetch_array(mysql_query("SELECT * FROM `discussions_set` WHERE `id_user` = '".$a['id']."' LIMIT 1")); // Общая настройка обсуждений

if ($f['disc_forum']==1 && $discSet['disc_forum']==1) /* Фильтр рассылки */
{

	// друзьям автора
	if (mysql_result(mysql_query("SELECT COUNT(*) FROM `discussions` WHERE `id_user` = '$a[id]' AND `type` = 'obmen' AND `id_sim` = '$file_id[id]' LIMIT 1"),0)==0)
	{
	if ($file_id['id_user']!=$a['id'] || $a['id'] != $user['id'])
	mysql_query("INSERT INTO `discussions` (`id_user`, `avtor`, `type`, `time`, `id_sim`, `count`) values('$a[id]', '$file_id[id_user]', 'obmen', '$time', '$file_id[id]', '1')"); 
	}
	else
	{
	$disc = mysql_fetch_array(mysql_query("SELECT * FROM `discussions` WHERE `id_user` = '$file_id[id_user]' AND `type` = 'obmen' AND `id_sim` = '$file_id[id]' LIMIT 1"));
	if ($file_id['id_user']!=$a['id'] || $a['id']!= $user['id'])
	mysql_query("UPDATE `discussions` SET `count` = '".($disc['count']+1)."', `time` = '$time' WHERE `id_user` = '$a[id]' AND `type` = 'obmen' AND `id_sim` = '$file_id[id]' LIMIT 1");
	}

}

}

// отправляем автору
if (mysql_result(mysql_query("SELECT COUNT(*) FROM `discussions` WHERE `id_user` = '$file_id[id_user]' AND `type` = 'obmen' AND `id_sim` = '$file_id[id]' LIMIT 1"),0)==0)
{
if ($file_id['id_user'] != $user['id'])
mysql_query("INSERT INTO `discussions` (`id_user`, `avtor`, `type`, `time`, `id_sim`, `count`) values('$file_id[id_user]', '$file_id[id_user]', 'obmen', '$time', '$file_id[id]', '1')"); 
}
else
{
$disc = mysql_fetch_array(mysql_query("SELECT * FROM `discussions` WHERE `id_user` = '$file_id[id_user]' AND `type` = 'obmen' AND `id_sim` = '$file_id[id]' LIMIT 1"));
if ($file_id['id_user'] != $user['id'])
mysql_query("UPDATE `discussions` SET `count` = '".($disc['count']+1)."', `time` = '$time' WHERE `id_user` = '$file_id[id_user]' AND `type` = 'obmen' AND `id_sim` = '$file_id[id]' LIMIT 1");
}

		/*
		==========================
		Уведомления об ответах
		==========================
		*/
		if (isset($user) && $respons==TRUE){
		$notifiacation=mysql_fetch_assoc(mysql_query("SELECT * FROM `notification_set` WHERE `id_user` = '".$ank_otv['id']."' LIMIT 1"));
			
			if ($notifiacation['komm'] == 1 && $ank_otv['id'] != $user['id'])
			mysql_query("INSERT INTO `notification` (`avtor`, `id_user`, `id_object`, `type`, `time`) VALUES ('$user[id]', '$ank_otv[id]', '$file_id[id]', 'files_komm', '$time')");
		
		}


mysql_query("INSERT INTO `obmennik_komm` (`id_file`, `id_user`, `time`, `msg`) values('$file_id[id]', '$user[id]', '$time', '".my_esc($msg)."')");
mysql_query("UPDATE `user` SET `balls` = '".($user['balls']+1)."', `rating_tmp` = '".($user['rating_tmp']+1)."' WHERE `id` = '$user[id]' LIMIT 1");
$_SESSION['message']='Сообщение успешно добавлено';
header ("Location: ?id_file=$file_id[id]");
exit;
}
}

err();
aut(); // форма авторизации



echo "<div class='foot'>";
echo "<img src='/style/icons/up_dir.gif' alt='*'> ".($dir['osn']==1?'<a href="/user/personalfiles/'.$ank['id'].'/'.$dir['id'].'/">Файлы</a>':'')." ".user_files($dir['id_dires'])." ".($dir['osn']==1?'':'&gt; <a href="/user/personalfiles/'.$ank['id'].'/'.$dir['id'].'/">'.htmlspecialchars($dir['name']).'</a>')."\n";
echo "</div>";


/*--------------------Папка под паролем--------------------*/
if ($dir['pass']!=NULL)
{
if (isset($_POST['password']))
{
$_SESSION['pass']=my_esc($_POST['password']);
if ($_SESSION['pass']!=$dir['pass'])
{$_SESSION['message'] = 'Неверный пароль'; $_SESSION['pass']=NULL;}
header("Location: ?");
}

if (!user_access('obmen_dir_edit') && ($user['id']!=$ank['id'] && $_SESSION['pass']!=$dir['pass']))
{
echo '<form action="?id_file='.$file_id['id'].'" method="POST">Пароль: <br />		<input type="pass" name="password" value="" /><br />		
<input type="submit" value="Войти"/></form>';
echo "<div class='foot'>";
echo "<img src='/style/icons/up_dir.gif' alt='*'> ".($dir['osn']==1?'Файлы':'')." ".user_files($dir['id_dires'])." ".($dir['osn']==1?'':'&gt; '.htmlspecialchars($dir['name']))."\n";
echo "</div>";
include_once '../../sys/inc/tfoot.php';
exit;
}
}
/*---------------------------------------------------------*/


 // Инклудим редактор
if (isset($user) && user_access('obmen_file_edit') || $ank['id']==$user['id'])
include "inc/file.edit.php";

 // Инклудим удаление
if (isset($user) && user_access('obmen_file_delete') || $ank['id']==$user['id'])
include "inc/file.delete.php";


echo '<div class="main">';
if ($dir_id['my']!=1)
{
if ($user['id']==$file_id['id_user'])
echo '<img src="/style/icons/z.gif" alt="*"> Зона обмена: <a href="/obmen'.$dir_id['dir'].'">'.$dir_id['name'].'</a> <a href="/obmen/?trans='.$file_id['id'].'"><img src="/style/icons/edit.gif" alt="*"></a><br />';
else
echo '<img src="/style/icons/z.gif" alt="*"> Зона обмена: <a href="/obmen'.$dir_id['dir'].'">'.$dir_id['name'].'</a><br /> ';
}


include_once H.'obmen/inc/icon14.php';
echo htmlspecialchars($file_id['name']).'.'.$ras.' ';
if ($file_id['metka'] == 1)echo '<font color=red><b>(18+)</b></font> ';
echo vremja($file_id['time']).'<br />';
echo '</div>';

if (($user['abuld'] == 1 || $file_id['metka'] == 0 || $file_id['id_user'] == $user['id'])) // Метка 18+ 
{
echo '<div class="main">';
if(is_file(H."obmen/inc/file/$ras.php"))include H."obmen/inc/file/$ras.php";
else
include_once H.'obmen/inc/file.php';
echo '</div>';
}elseif (!isset($user)){
echo '<div class="mess">';
echo '<img src="/style/icons/small_adult.gif" alt="*"><br /> Данный файл содержит изображения эротического характера. Только зарегистрированные пользователи старше 18 лет могут просматривать такие файлы. <br />';
echo '<a href="/aut.php">Вход</a> | <a href="/reg.php">Регистрация</a>';
echo '</div>';
}else{
echo '<div class="mess">';
echo '<img src="/style/icons/small_adult.gif" alt="*"><br /> 
	Данный файл содержит изображения эротического характера. 
	Если Вас это не смущает и Вам 18 или более лет, то можете <a href="?id_file='.$file_id['id'].'&amp;sess_abuld=1">продолжить просмотр</a>. 
	Или Вы можете отключить предупреждения в <a href="/user/info/settings.php">настройках</a>.';
	echo '</div>';
}


/*----------------------листинг-------------------*/
$listr = mysql_fetch_assoc(mysql_query("SELECT * FROM `obmennik_files` WHERE `my_dir` = '$dir[id]' AND `id` < '$file_id[id]' ORDER BY `id` DESC LIMIT 1"));
$list = mysql_fetch_assoc(mysql_query("SELECT * FROM `obmennik_files` WHERE `my_dir` = '$dir[id]' AND `id` > '$file_id[id]' ORDER BY `id`  ASC LIMIT 1"));
echo '<div class="c2" style="text-align: center;">';
echo '<span class="page">'.($list['id']?'<a href="?id_file='.$list['id'].'">&laquo; Пред.</a> ':'&laquo; Пред. ').'</span>';

$k_1=mysql_result(mysql_query("SELECT COUNT(*) FROM `obmennik_files` WHERE `id` > '$file_id[id]' AND `my_dir` = '$dir[id]'"),0)+1;
$k_2=mysql_result(mysql_query("SELECT COUNT(*) FROM `obmennik_files` WHERE `my_dir` = '$dir[id]'"),0);
echo ' ('.$k_1.' из '.$k_2.') ';

echo '<span class="page">'.($listr['id']?'<a href="?id_file='.$listr['id'].'">След. &raquo;</a>':' След. &raquo;').'</span>';
echo '</div>';
/*----------------------alex-borisi---------------*/



if (($user['abuld'] == 1 || $file_id['metka'] == 0 || $file_id['id_user'] == $user['id'])) // Метка 18+ 
{
/*----------------Действия над файлом-------------*/
if (user_access('obmen_file_edit') || $user['id']==$file_id['id_user'])
{
	echo '<div class="main">';
		if ($user['id']==$file_id['id_user'] && $dir_id['my']==1)echo '[<a href="/obmen/?trans='.$file_id['id'].'"><img src="/style/icons/z.gif" alt="*"> в зону</a>]';
	echo ' [<img src="/style/icons/edit.gif" alt="*"> <a href="?id_file='.$file_id['id'].'&amp;edit">ред.</a>]';
	echo ' [<img src="/style/icons/delete.gif" alt="*"> <a href="?id_file='.$file_id['id'].'&amp;delete">удл.</a>]';
	echo '</div>';
}
/*----------------------alex-borisi---------------*/


echo '<div class="main">';
if (isset($user) && $ank['id'] != $user['id'] && mysql_result(mysql_query("SELECT COUNT(*) FROM `like_object` WHERE `id_object` = '$file_id[id]' AND `type` = 'obmen' AND `id_user` = '$user[id]'"),0)==0)
{
echo '[<img src="/style/icons/like.gif" alt="*"> <a href="?id_file='.$file_id['id'].'&amp;like=1">Мне нравится</a>] ';
echo '[<a href="?id_file='.$file_id['id'].'&amp;like=0"><img src="/style/icons/dlike.gif" alt="*"></a>]';

}else{
echo '[<img src="/style/icons/like.gif" alt="*"> 
'.mysql_result(mysql_query("SELECT COUNT(*) FROM `like_object` WHERE `id_object` = '$file_id[id]' AND `type` = 'obmen' AND `like` = '1'"),0).'] ';
echo '[<img src="/style/icons/dlike.gif" alt="*"> 
'.mysql_result(mysql_query("SELECT COUNT(*) FROM `like_object` WHERE `id_object` = '$file_id[id]' AND `type` = 'obmen' AND `like` = '0'"),0).']';
}
echo '</div>';

echo '<div class="main">';
if ($file_id['ras']=='jar')
echo '<img src="/style/icons/d.gif" alt="*"> <a href="/obmen'.$dir_id['dir'].$file_id['id'].'.'.$file_id['ras'].'">Скачать JAR ('.size_file($size).')</a> <a href="/obmen'.$dir_id['dir'].$file_id['id'].'.jad">JAD</a> <br />';
else
echo '<img src="/style/icons/d.gif" alt="*"> <a href="/obmen'.$dir_id['dir'].$file_id['id'].'.'.$file_id['ras'].'">Скачать ('.size_file($size).')</a><br />';
echo 'Скачан ('.$file_id['k_loads'].')';
echo '</div>';


/*-------------------Моя музыка---------------------*/
if (isset($user) && ($file_id['ras']=='mp3' || $file_id['ras']=='wav' || $file_id['ras']=='ogg'))
{
echo '<div class="main">';
if ($music==0)
echo '<a href="?id_file='.$file_id['id'].'&amp;play=1"><img src="/style/icons/play.png" alt="*"></a> ('.$music_people.')';
else
echo '<a href="?id_file='.$file_id['id'].'&amp;play=0"><img src="/style/icons/play.png" alt="*"></a> ('.$music_people.') <img src="/style/icons/ok.gif" alt="*">';
echo '</div>';
}
/*--------------------------------------------------*/
}
include_once 'inc/komm.php'; // комментарии

echo "<div class='foot'>";
echo "<img src='/style/icons/up_dir.gif' alt='*'> ".($dir['osn']==1?'<a href="/user/personalfiles/'.$ank['id'].'/'.$dir['id'].'/">Файлы</a>':'')." ".user_files($dir['id_dires'])." ".($dir['osn']==1?'':'&gt; <a href="/user/personalfiles/'.$ank['id'].'/'.$dir['id'].'/">'.htmlspecialchars($dir['name']).'</a>')."\n";
echo "</div>";

?>
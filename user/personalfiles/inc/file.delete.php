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


	// Удаляем файл
if (isset($_GET['delete']))
{

if (!isset($_GET['page']))$_GET['page'] = 1;
	if (isset($_GET['ok']))
	{
	mysql_query("DELETE FROM `user_music` WHERE `id_file` = '$file_id[id]' AND `dir` = 'obmen'");
	mysql_query("DELETE FROM `obmennik_files` WHERE `id` = '$file_id[id]'");
	unlink(H.'sys/obmen/files/'.$file_id['id'].'.dat');
	unlink(H.'sys/obmen/screens/128/'.$file_id['id'].'.gif');
	unlink(H.'sys/obmen/screens/128/'.$file_id['id'].'.png');
	unlink(H.'sys/obmen/screens/128/'.$file_id['id'].'.jpg');
	unlink(H.'sys/obmen/screens/128/'.$file_id['id'].'.jpeg');
	unlink(H.'sys/obmen/screens/48/'.$file_id['id'].'.gif');
	unlink(H.'sys/obmen/screens/48/'.$file_id['id'].'.png');
	unlink(H.'sys/obmen/screens/48/'.$file_id['id'].'.jpg');
	unlink(H.'sys/obmen/screens/48/'.$file_id['id'].'.jpeg');
	unlink(H.'sys/obmen/files/'.$file_id['id'].'.dat');	
	$_SESSION['message']='Файл успешно удален';
	
	
	header ("Location: ?page=".intval($_GET['page'])."");
	exit;
	}
	
	echo '<div class="mess">';
	echo 'Удалить файл '.htmlspecialchars($file_id['name']).'?<br />';
	echo '</div>';	
	
	echo '<div class="main">';
	echo '[<a href="?page='.intval($_GET['page']).'&amp;id_file='.$file_id['id'].'&amp;delete&amp;ok"><img src="/style/icons/ok.gif" alt="*"> Да</a>] ';
	echo '[<a href="?page='.intval($_GET['page']).'&amp;id_file='.$file_id['id'].'"><img src="/style/icons/delete.gif" alt="*"> Нет</a>]';
	echo '</div>';	
	include_once '../../sys/inc/tfoot.php';	
	exit;
}


?>
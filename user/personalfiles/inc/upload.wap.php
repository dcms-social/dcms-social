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

if (isset($_SESSION['obmen_dir']))
{
	$dir_id = mysql_fetch_assoc(mysql_query("SELECT * FROM `obmennik_dir` WHERE `id` = '" . intval($_SESSION['obmen_dir']) . "' LIMIT 1"));
}else{
	$dir_id = mysql_fetch_assoc(mysql_query("SELECT * FROM `obmennik_dir` WHERE `my` = '1' LIMIT 1"));
}



if ($dir_id['upload']==1){

if (isset($_GET['upload']) && $_GET['upload']=='enter')
{
if (!isset($_FILES['file']))$err[]='Ошибка при выгрузке файла';
elseif (!isset($_FILES['file']['tmp_name']) || filesize($_FILES['file']['tmp_name'])>$dir_id['maxfilesize'])$err[]='Размер файла превышает установленные ограничения';
else
{

$file=esc(stripcslashes(htmlspecialchars($_FILES['file']['name'])));

$file=preg_replace('(\#|\?)', NULL, $file);

$name=preg_replace('#\.[^\.]*$#', NULL, $file); // имя файла без расширения
$ras=strtolower(preg_replace('#^.*\.#', NULL, $file));
$type=$_FILES['file']['type'];
$size=filesize($_FILES['file']['tmp_name']);
$rasss=explode(';', $dir_id['ras']);
$ras_ok=false;
for($i=0;$i<count($rasss);$i++)
{
if ($rasss[$i]!=NULL && $ras==$rasss[$i])$ras_ok=true;
}

if (!$ras_ok)$err='Неверное расширение файла';
}

if (isset($_POST['metka']) && ($_POST['metka'] == '0' || $_POST['metka'] == '1'))$metka = $_POST['metka'];
else $metka = 0;

$opis=NULL;
if (isset($_POST['msg']))
$opis=stripslashes(htmlspecialchars(esc($_POST['msg'])));


if (!isset($err))
{
mysql_query("UPDATE `user` SET `rating_tmp` = '".($user['rating_tmp']+3)."' WHERE `id` = '$user[id]' LIMIT 1");
mysql_query("INSERT INTO `obmennik_files` (`metka`, `id_dir`, `name`, `ras`, `type`, `size`, `time`, `time_last`, `id_user`, `opis`, `my_dir` )
VALUES ('$metka', '$dir_id[id]', '$name', '$ras', '$type', '$size', '$time', '$time', '$user[id]', '$opis' , '$dir[id]')");
$id_file=mysql_insert_id();


/*----------------------Лента------------------------*/
if (!$dir['pass'])
{
$q = mysql_query("SELECT * FROM `frends` WHERE `user` = '".$dir['id_user']."' AND `i` = '1'"); /* Список друзей пользователя */
while ($f = mysql_fetch_array($q))
{
$a=get_user($f['frend']);
$lentaSet = mysql_fetch_array(mysql_query("SELECT * FROM `tape_set` WHERE `id_user` = '".$a['id']."' LIMIT 1")); // Общая настройка ленты
if ($f['lenta_obmen']==1 && $lentaSet['lenta_files']==1) /* Фильтр рассылки */
{
	if (mysql_result(mysql_query("SELECT COUNT(*) FROM `tape` WHERE `id_user` = '$a[id]' AND `type` = 'obmen' AND `id_file` = '$dir[id]'"),0)==0)
	{ 
		/* Если нет в ленте этой папки */
		mysql_query("INSERT INTO `tape` (`id_user`, `avtor`, `type`, `time`, `id_file`, `count`) values('$a[id]', '$dir[id_user]', 'obmen', '$time', '$dir[id]', '1')"); 
	}
	elseif (mysql_result(mysql_query("SELECT COUNT(*) FROM `tape` WHERE `id_user` = '$a[id]' AND `type` = 'obmen' AND `id_file` = '$dir[id]' AND `read` = '1'"),0)>0)
	{ 
		/* Если папка есть в ленте то удаляем запись и создаем новую */
		mysql_query("DELETE FROM `tape` WHERE `id_user` = '$a[id]' AND `type` = 'obmen' AND `id_file` = '$dir[id]'");
		mysql_query("INSERT INTO `tape` (`id_user`, `avtor`, `type`, `time`, `id_file`, `count`) values('$a[id]', '$dir[id_user]', 'obmen', '$time', '$dir[id]', '1')"); 
	}
	else
	{
		/* Обновляем колличество новых файлов */
		$tape = mysql_fetch_array(mysql_query("SELECT * FROM `tape` WHERE `id_user` = '$a[id]' AND `type` = 'obmen' AND `id_file` = '$dir[id]'"));
		mysql_query("UPDATE `tape` SET `count` = '".($tape['count']+1)."', `read` = '0', `time` = '$time' WHERE `id_user` = '$a[id]' AND `type` = 'obmen' AND `id_file` = '$dir[id]' LIMIT 1");
	}
}
}
}
/*-------------------alex-borisi--------------------*/


if (!@copy($_FILES['file']['tmp_name'], H."sys/obmen/files/$id_file.dat"))
{
mysql_query("DELETE FROM `obmennik_files` WHERE `id` = '$id_file' LIMIT 1");
$err[]='Ошибка при выгрузке';
}
}

if (!isset($err))
{

chmod(H."sys/obmen/files/$id_file.dat", 0666);

if (isset($_FILES['screen']) && $imgc=@imagecreatefromstring(file_get_contents($_FILES['screen']['tmp_name'])))
{
$img_x=imagesx($imgc);
$img_y=imagesy($imgc);
if ($img_x==$img_y)
{
$dstW=320; // ширина
$dstH=320; // высота 
}
elseif ($img_x>$img_y)
{
$prop=$img_x/$img_y;
$dstW=320;
$dstH=ceil($dstW/$prop);
}
else
{
$prop=$img_y/$img_x;
$dstH=320;
$dstW=ceil($dstH/$prop);
}



$screen=imagecreatetruecolor($dstW, $dstH);
imagecopyresampled($screen, $imgc, 0, 0, 0, 0, $dstW, $dstH, $img_x, $img_y);
imagedestroy($imgc);
$screen=img_copyright($screen); // наложение копирайта
imagegif($screen,H."sys/obmen/screens/320/$id_file.gif");
imagedestroy($screen);

}


if (isset($_FILES['screen']) && $imgc=@imagecreatefromstring(file_get_contents($_FILES['screen']['tmp_name'])))
{
$img_x=imagesx($imgc);
$img_y=imagesy($imgc);
if ($img_x==$img_y)
{
$dstW=128; // ширина
$dstH=128; // высота 
}
elseif ($img_x>$img_y)
{
$prop=$img_x/$img_y;
$dstW=128;
$dstH=ceil($dstW/$prop);
}
else
{
$prop=$img_y/$img_x;
$dstH=128;
$dstW=ceil($dstH/$prop);
}



$screen=imagecreatetruecolor($dstW, $dstH);
imagecopyresampled($screen, $imgc, 0, 0, 0, 0, $dstW, $dstH, $img_x, $img_y);
imagedestroy($imgc);
$screen=img_copyright($screen); // наложение копирайта
imagegif($screen,H."sys/obmen/screens/128/$id_file.gif");
imagedestroy($screen);


}

$_SESSION['obmen_dir'] = null;
$_SESSION['message'] = 'Файл успешно выгружен';
header('Location: ?');
exit;
}
}

 
}

if ($dir_id['upload']==1 && isset($user))
{


$set['title'] = 'Загрузка файла';
include_once '../../sys/inc/thead.php';
title();
aut();
err();


echo "<div class='foot'>";
echo "<img src='/style/icons/up_dir.gif' alt='*'> ".($dir['osn']==1?'<a href="/user/personalfiles/'.$ank['id'].'/'.$dir['id'].'/">Файлы</a>':'')." ".user_files($dir['id_dires'])." ".($dir['osn']==1?'':'&gt; <a href="/user/personalfiles/'.$ank['id'].'/'.$dir['id'].'/">'.text($dir['name']).'</a>')."\n";
echo "</div>";

if (isset($_SESSION['obmen_dir']))
{
	echo '<div class="mess">';
	echo 'Файл будет загружен в папку <b>' . text($dir_id['name']) . '</b> зоны обмена ';
	echo '</div>';		
}

echo "<form class='foot' enctype=\"multipart/form-data\" name='message' action='?upload=enter&wap' method=\"post\">
	 Файл: (<".size_file($dir_id['maxfilesize']).")<br />
	 <input name='file' type='file' maxlength='$dir_id[maxfilesize]' /><br />
	 Скриншот:<br />
	 <input name='screen' type='file' accept='image/*' /><br />";	 	
		 
	if ($set['web'] && is_file(H.'style/themes/'.$set['set_them'].'/altername_post_form.php'))		
	include_once H.'style/themes/'.$set['set_them'].'/altername_post_form.php';	
	else
	{
		echo $tPanel . '<textarea name="msg"></textarea><br />';	
	}	
	 
	echo "<label><input type='checkbox' name='metka' value='1' /> Метка <font color=red>18+</font></label><br />";

	 echo "<input class=\"submit\" type=\"submit\" value=\"Выгрузить\" /> [<img src='/style/icons/delete.gif' alt='*'> <a href='?'>Отмена</a>]<br />
	 <div class='main'>*Разрешается выгружать файлы форматов: ";
	 
$i5=explode(';', $dir_id['ras']);
for ($i = 0; $i < count($i5); $i++) 
{
echo $i5[$i].', ';
}
echo "если нехватает какого то формата, просьба сообщить об этом администрации проекта!</div></form>";
echo "<div class='foot'>";
echo "<img src='/style/icons/up_dir.gif' alt='*'> ".($dir['osn']==1?'<a href="/user/personalfiles/'.$ank['id'].'/'.$dir['id'].'/">Файлы</a>':'')." ".user_files($dir['id_dires'])." ".($dir['osn']==1?'':'&gt; <a href="/user/personalfiles/'.$ank['id'].'/'.$dir['id'].'/">'.text($dir['name']).'</a>')."\n";
echo "</div>";


}


?>
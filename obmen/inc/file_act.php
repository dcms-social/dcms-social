<?
if ((user_access('obmen_file_delete') || $user['id']==$file_id['id_user'])  && isset($_GET['act']) && $_GET['act']=='edit' && isset($_GET['ok']) && $l!='/')
{
	$name=my_esc($_POST['name']);
	$opis=my_esc($_POST['opis']);
	if(strlen2($name)<2)$err[]='Короткое Название';
	if(strlen2($name)>128)$err[]='Длинное Название';
	if ($_POST['metka'] == 0 || $_POST['metka'] == 1)$metka = $_POST['metka'];
	else $err = 'Ошибка метки +18';
	if(!isset($err)){
		mysql_query("UPDATE `obmennik_files` SET `metka` = '".$metka."', `name` = '".$name."',`opis` = '".$opis."' WHERE `id` = '$file_id[id]' LIMIT 1");
		$_SESSION['message']='Файл успешно отредактирован';
		admin_log('Обменник','Редактирование файла', "Редактирование файла [url=/obmen$dir_id[dir]$name.$file_id[ras]?showinfo]$file_id[name][/url]");
		header ("Location: /obmen$dir_id[dir]$file_id[id].$file_id[ras]?showinfo");
		exit;
	}
}
if ((user_access('obmen_file_delete') or $user['id']==$file_id['id_user']) && isset($_GET['act']) && $_GET['act']=='delete' && isset($_GET['ok']) && $l!='/')
{
	mysql_query("DELETE FROM `obmennik_files` WHERE `id` = '$file_id[id]'");
	mysql_query("DELETE FROM `user_music` WHERE `id_file` = '$file_id[id]' AND `dir` = 'obmen'");
	unlink(H.'sys/obmen/files/'.$file_id['id'].'.dat');	$_SESSION['message']='Файл успешно удален';
	header ("Location: /obmen$dir_id[dir]?".SID);
	exit;
}

?>
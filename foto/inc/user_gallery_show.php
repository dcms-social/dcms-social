<?
if (!isset($user) && !isset($_GET['id_user'])){ header("Location: /foto/?".SID);exit; }
if (isset($user))$ank['id'] = $user['id'];
if (isset($_GET['id_user']))$ank['id'] = intval($_GET['id_user']);

// Автор альбома
$ank = get_user($ank['id']);

if (!$ank){header('Location: /foto/?' . SID);exit;}

// Если вы в бане 
if (mysql_result(mysql_query("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'foto' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0)!=0)
{
	header('Location: /ban.php?'.SID);
	exit;
}

// Альбом
$gallery['id'] = intval($_GET['id_gallery']);

if (mysql_result(mysql_query("SELECT COUNT(*) FROM `gallery` WHERE `id` = '$gallery[id]' AND `id_user` = '$ank[id]' LIMIT 1"),0) == 0)
{
	header('Location: /foto/' . $ank['id'] . '/?' . SID);
	exit;
}

$gallery = mysql_fetch_assoc(mysql_query("SELECT * FROM `gallery` WHERE `id` = '$gallery[id]' AND `id_user` = '$ank[id]' LIMIT 1"));

// заголовок страницы
$set['title'] = user::nick($ank['id'], 0) . ' - ' . text($gallery['name']); 

// Редактирование альбома и загрузка фото
include 'inc/gallery_show_act.php';

include_once '../sys/inc/thead.php';
title();
aut();
err();

// Формы
include 'inc/gallery_show_form.php';

echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*"> ' . user::nick($ank['id']) . ' | <a href="/foto/' . $ank['id'] . '/">Альбомы</a> | <b>' . text($gallery['name']) . '</b></div>';


// Подключаем приватность стр. 
include H.'sys/add/user.privace.php';

/*
* Если установлена приватность альбома
*/	
if ($gallery['privat'] == 1 && ($frend != 2 || !isset($user)) && $user['level'] <= $ank['level'] && $user['id'] != $ank['id'])
{
	echo '<div class="mess">';
	echo 'Просматривать альбом пользователя могут только его друзья!';
	echo '</div>';

	$block_foto = true;
}
elseif ($gallery['privat'] == 2 && $user['id'] != $ank['id'] && $user['level'] <= $ank['level'])
{
	echo '<div class="mess">';
	echo 'Пользователь запретил просмотр этого альбома!';
	echo '</div>';
	
	$block_foto = true;
}

/*--------------------Альбом под паролем-------------------*/
if ($user['id'] != $ank['id'] && $gallery['pass'] != NULL)
{
	if (isset($_POST['password']))
	{
		$_SESSION['pass'] = my_esc($_POST['password']);
		
		if ($_SESSION['pass'] != $gallery['pass'])
		{
			$_SESSION['message'] = 'Неверный пароль'; 
			$_SESSION['pass'] = NULL;
		}
		header("Location: ?");
	}

	if (!isset($_SESSION['pass']) || $_SESSION['pass'] != $gallery['pass'])
	{
		echo '<form action="?" method="POST">Пароль:<br /><input type="pass" name="password" value="" /><br />		
		<input type="submit" value="Войти"/></form>';
		
		echo '<div class="foot">';
		echo '<img src="/style/icons/str2.gif" alt="*"> ' . user::nick($ank['id']) . ' | <a href="/foto/' . $ank['id'] . '/">Альбомы</a> | <b>' . text($gallery['name']) . '</b>';
		echo '</div>';

		include_once '../sys/inc/tfoot.php';
		exit;
	}
}
/*---------------------------------------------------------*/

if (!isset($block_foto))
{
	$k_post = mysql_result(mysql_query("SELECT COUNT(*) FROM `gallery_foto` WHERE `id_gallery` = '$gallery[id]'"),0);
	$k_page = k_page($k_post,$set['p_str']);
	$page = page($k_page);
	$start = $set['p_str']*$page-$set['p_str'];

	echo '<table class="post">';

	if ($k_post == 0)
	{
		echo '<div class="mess">';
		echo 'Фотографий нет';
		echo '</div>';
	}

	$q = mysql_query("SELECT * FROM `gallery_foto` WHERE `id_gallery` = '$gallery[id]' ORDER BY `id` DESC LIMIT $start, $set[p_str]");
	
	while ($post = mysql_fetch_assoc($q))
	{
		// Лесенка
		echo '<div class="' . ($num % 2 ? "nav1" : "nav2") . '">';
		$num++;
		
		echo '<img src="/style/themes/' . $set['set_them'] . '/loads/14/jpg.png" alt="*"/>';
		echo '<a href="/foto/' . $ank['id'] . '/' . $gallery['id'] . '/' . $post['id'] . '/">' . text($post['name']);
		
		if ($post['metka'] == 1)echo ' <font color=red>(18+)</font>';
		
		echo '<br /><img src="/foto/foto128/' . $post['id'] . '.' . $post['ras'] . '" alt="Photo Screen" /></a><br />';
		
		if ($post['opis'] == null)
			echo 'Без описания<br />';
		else 
			echo '<div class="text">' . output_text($post['opis']) . '</div>';
		
		echo '<img src="/style/icons/uv.png"> (' . mysql_result(mysql_query("SELECT COUNT(*) FROM `gallery_komm` WHERE `id_foto` = '$post[id]'"),0) . ')';
		echo '<img src="/style/icons/add_fav.gif"> ('.mysql_result(mysql_query("SELECT COUNT(`id`)FROM `bookmarks` WHERE `id_object`='".$post['id']."' AND `type`='foto'"),0).')';
		
		echo '</div>';
	}
	
	echo '</table>';
	
	// Вывод страниц
	if ($k_page > 1)str('?', $k_page, $page); 
}
if(isset($user) && (user_access('foto_alb_del') || $ank['id']==$user['id'])){
echo '<div class="mess">';
echo '<img src="/style/icons/apply14.png" width="16"> <a href="?act=upload">Загрузить фотку</a><br/>';
echo '<img src="/style/icons/edit.gif" width="16"> <a href="/foto/' . $ank['id'] . '/' . $gallery['id'] . '/?edit=rename">Редактировать альбом</a><br/>';
echo '<img src="/style/icons/delete.gif" width="16"> <a href="/foto/' . $ank['id'] . '/' . $gallery['id'] . '/?act=delete"">Удалить альбом</a></div>';
}
echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*"> ' . user::nick($ank['id']) . ' | <a href="/foto/' . $ank['id'] . '/">Альбомы</a> | <b>' . text($gallery['name']) . '</b>';
echo '</div>';

include_once '../sys/inc/tfoot.php';
exit;
?>
<?
/* Бан пользователя */ 
if (mysql_result(mysql_query("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'foto' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0)!=0)
{
	header('Location: /ban.php?'.SID);
	exit;
}

$set['title'] = 'Фотоальбомы'; // заголовок страницы

include_once '../sys/inc/thead.php';
title();
aut();

$k_post = mysql_result(mysql_query("SELECT COUNT(*) FROM `gallery`"),0);
$k_page = k_page($k_post,$set['p_str']);
$page = page($k_page);
$start = $set['p_str']*$page-$set['p_str'];


echo '<table class="post">';

if ($k_post == 0)
{
	echo '<div class="mess">';
	echo 'Нет фотоальбомов';
	echo '</div>';
}

$q = mysql_query("SELECT * FROM `gallery` ORDER BY `time` DESC LIMIT $start, $set[p_str]");

while ($post = mysql_fetch_assoc($q))
{
	$ank = get_user($post['id_user']);

	// Лесенка
	echo '<div class="' . ($num % 2 ? "nav1" : "nav2") . '">';
	$num++;

	echo '<img src="/style/themes/' . $set['set_them'] . '/loads/14/' . ($post['pass'] != null || $post['privat'] != 0 ? 'lock.gif' : 'dir.png') . '" alt="*" /> ';

	echo '<a href="/foto/' . $ank['id'] . '/' . $post['id'] . '/">' . text($post['name']) . '</a> (' . mysql_result(mysql_query("SELECT COUNT(*) FROM `gallery_foto` WHERE `id_gallery` = '$post[id]'"),0) . ' фото)<br />';

	if ($post['opis'] == null)
	echo 'Без описания<br />';
	else 
	echo output_text($post['opis']) . '<br />';

	echo 'Создан: ' . vremja($post['time_create']) . '<br />';

	echo 'Автор: ';
	echo user::avatar($ank['id'], 2) . user::nick($ank['id'], 1, 1, 1);

	echo '</div>';
}

echo '</table>';

if ($k_page>1)str('?',$k_page,$page); // Вывод страниц

if (isset($user))
{
	echo '<div class="foot">';
	echo '<img src="/style/icons/str.gif" alt="*"> <a href="/foto/' . $user['id'] . '/">Мои альбомы</a><br />';
	echo '</div>';
}

include_once '../sys/inc/tfoot.php';
exit;
?>
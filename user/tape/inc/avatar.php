<?
/*
* $name описание действий объекта 
*/
if ($type=='avatar' && $post['avtor'] != $user['id']) // аватар
{
	if ($post['avatar'])
	$name = 'сменил' . ($avtor['pol'] == 1 ? null : "а") . ' фото на главной';
	else
	$name = 'установил' . ($avtor['pol'] == 1 ? null : "а") . ' фото на главной';	
}

/*
* Вывод блока с содержимым 
*/
if ($type == 'avatar')
{
	$foto = mysql_fetch_assoc(mysql_query("SELECT * FROM `gallery_foto` WHERE `id` = '".$post['id_file']."' LIMIT 1"));
	$avatar = mysql_fetch_assoc(mysql_query("SELECT * FROM `gallery_foto` WHERE `id` = '".$post['avatar']."' LIMIT 1"));
	$gallery = mysql_fetch_assoc(mysql_query("SELECT * FROM `gallery` WHERE `id` = '".$foto['id_gallery']."' LIMIT 1"));
	$gallery2 = mysql_fetch_assoc(mysql_query("SELECT * FROM `gallery` WHERE `id` = '".$avatar['id_gallery']."' LIMIT 1"));

	echo '<div class="nav1">';
	echo avatar($avtor['id']) . group($avtor['id']) . user::nick($avtor['id']);
	echo medal($avtor['id']) . online($avtor['id']) . ' <a href="user.settings.php?id=' . $avtor['id'] . '">[!]</a> ' . $name;
	echo $s1 . vremja($post['time']) . $s2;
	echo '</div>';

	echo '<div class="nav2">';
	if ($foto['id'])echo '<b>' . text($foto['name']) . '</b>';
	if ($avatar['id'])echo ' &raquo; <b>' . text($avatar['name']) . '</b>';
	if ($avatar['id'] || $foto['id'])echo '<br />';
	
	
	if ($foto['id'])echo '<a href="/foto/' . $avtor['id'] . '/' . $gallery['id'] . '/' . $foto['id'] . '/">';
	echo '<img style=" max-width:50px; margin:3px;" src="/foto/foto50/' . $post['id_file'] . '.jpg" alt="*" />';
	if ($foto['id'])echo '</a>';
	
	if ($post['avatar'])
	{
		echo ' <img src="/style/icons/arRt2.png" alt="*"/> ';
		if ($avatar['id'])echo '<a href="/foto/' . $avtor['id'] . '/' . $gallery2['id'] . '/' . $avatar['id'] . '/">';
		echo '<img style="max-width:50px; margin:3px;" src="/foto/foto50/' . $post['avatar'] . '.jpg" alt="*" />';
		if ($avatar['id'])echo '</a>';
	}
	
	echo '<br />';
	
	if ($foto['id'])
	echo '<a href="/foto/' . $avtor['id'] . '/' . $gallery['id'] . '/' . $foto['id'] . '/"><img src="/style/icons/bbl5.png" alt="*"/> (' . mysql_result(mysql_query("SELECT COUNT(*) FROM `gallery_komm` WHERE `id_foto` = '$foto[id]'"),0) . ')</a> ';
}
?>
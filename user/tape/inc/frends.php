<?
/*
* $name описание действий объекта 
*/
if ($type == 'frends' && $post['avtor'] != $user['id']) // дневники
{
	$name = 'добавил' . ($avtor['pol'] == 1 ? null : "а") . ' в друзья';
}

/*
* Вывод блока с содержимым 
*/
if ($type == 'frends')
{
	$frend = get_user($post['id_file']);
	
	if ($frend['id'])
	{
		echo '<div class="nav1">';
		echo avatar($avtor['id']) . group($avtor['id']) . user::nick($avtor['id']);
		echo ' ' . medal($avtor['id']) . ' ' . online($avtor['id']) . ' <a href="user.settings.php?id=' . $avtor['id'] . '>[!]</a> ' . $name . ' ';
		
		echo avatar($frend['id']) . group($frend['id']) . user::nick($frend['id']);
		echo ' ' . medal($frend['id']) . ' ' . online($frend['id']) . ' ';
		
		echo $s1 . vremja($post['time']). $s2;
		echo '</div>';

		echo '<div class="nav2">';	
		
		if (mysql_result(mysql_query("SELECT COUNT(*) FROM `gallery_foto` WHERE `id_user` = '$frend[id]'"),0)>0)
		{
			echo 'Последние добавленные фото ' . user::nick($frend['id'], 0) . '<br />';
			
			$g = mysql_query("SELECT * FROM `gallery_foto` WHERE `id_user` = '$frend[id]' ORDER BY `id` DESC LIMIT 4");
			
			while ($xx = mysql_fetch_assoc($g))
			{
				$gallery = mysql_fetch_assoc(mysql_query("SELECT * FROM `gallery` WHERE `id` = '" . $xx['id_gallery'] . "' LIMIT 1"));
				echo "<a href='/foto/$gallery[id_user]/$gallery[id]/$xx[id]/'><img style=' margin: 2px;' src='/foto/foto50/$xx[id].$xx[ras]' alt='*'/></a>";
			}
		}
		else
		{
			echo 'У ' . user::nick($frend['id'], 0) . ' еще нет загруженных фотографий =(';
		}
	}
	else
	{
		echo '<div class="nav1">';
		echo 'Запись уничтожена =(';

	}
}
?>
<?
/*
* $name описание действий объекта 
*/
if ($type == 'notes' && $post['avtor'] != $user['id']) // дневники
{
	$name = 'создал' . ($avtor['pol'] == 1 ? null : "а") . ' новый дневник';
}

/*
* Вывод блока с содержимым 
*/
if ($type  ==  'notes')
{
	$notes = mysql_fetch_assoc(mysql_query("SELECT * FROM `notes` WHERE `id` = '" . $post['id_file'] . "' LIMIT 1"));

	if ($notes['id'])
	{
		echo '<div class="nav1">';
		echo avatar($avtor['id']) . group($avtor['id']) . user::nick($avtor['id']) . medal($avtor['id']) . online($avtor['id']) . 
		' <a href="user.settings.php?id=' . $avtor['id'] . '">[!]</a> ' . $name . '
		<b>' . text($notes['name']) . '</b> ' . $s1 . vremja($post['time']) . $s2 . '<br />';
		echo '</div>';
		
		echo '<div class="nav2" ><div class="text" >';
		echo output_text($notes['msg']) . '<br /></div>'; 
		
		echo '<a href="/plugins/notes/list.php?id=' . $notes['id'] . '"><img src="/style/icons/bbl5.png" alt="*"/> 
		(' . mysql_result(mysql_query("SELECT COUNT(*) FROM `notes_komm` WHERE `id_notes` = '$notes[id]'"),0) . ')</a>';
	}
	else
	{
		echo '<div class="nav1">';
		echo avatar($avtor['id']) . group($avtor['id']) . user::nick($avtor['id']) . ' <a href="user.settings.php?id=' . $avtor['id'] . '">[!]</a>';
		echo medal($avtor['id']) . online($avtor['id']) . "";
		echo "</div>";
		echo '<div class="nav2">';
		echo "Дневник уже удален =( $s1 " . vremja($post['time']) . " $s2";
	}
}
?>
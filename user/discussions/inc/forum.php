<?
/*
* Заголовок обсуждения
*/

if ($type == 'them' && $post['avtor'] != $user['id'])
{
	$name = __('Форум | Тема форума');
}
else if ($type == 'them' && $post['avtor'] == $user['id'])
{
	$name = __('Форум | Ваша тема');
}
 
/*
* Выводим на экран
*/
if ($type == 'them')
{
	$them = mysql_fetch_assoc(mysql_query("SELECT * FROM `forum_t` WHERE `id` = '".$post['id_sim']."' LIMIT 1"));
	
	if ($them['id'])
	{
		?>
		<div class="nav1">
		<img src="/style/icons/forum.png" alt="*"/> <a href="/forum/<?= $them['id_forum']?>/<?= $them['id_razdel']?>/<?= $them['id']?>/?page=<?= $pageEnd?>"><?= $name?></a> 
		<?
		if ($post['count'] > 0)
		{
			?><b><font color='red'>+<?= $post['count']?></font></b><?
		}
		?>
		<span class="time"><?= $s1 . vremja($post['time']) . $s2?></span>
		</div>
		
		<div class="nav2">
		<b><font color='green'><?= $avtor['nick']?></font></b> 
		<?= ($avtor['id'] != $user['id'] ? '<a href="user.settings.php?id=' . $avtor['id'] . '">[!]</a>' : '')?> 
		<?= $avtor['medal']?> <?= $avtor['online']?> &raquo; <b><?= text($them['name'])?></b><br />
		<span class="text"><?= output_text($them['text'])?></span>
		</div>
		<?
	}
	else
	{
		?>
		<div class="mess">
		<?= __('Тема форума уже удалена =(')?>
		<span class="time"><?= $s1 . vremja($post['time']) . $s2?></span>
		</div>
		<?
	}

}
?>
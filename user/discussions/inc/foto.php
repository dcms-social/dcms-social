<?
/*
* Заголовок обсуждения
*/

if ($type == 'foto' && $post['avtor'] != $user['id'])
{
	$name = __('Фотография друга');
}
else if ($type == 'foto' && $post['avtor'] == $user['id'])
{
	$name = __('Ваша фотография');
}
 
/*
* Выводим на экран
*/
if ($type == 'foto')
{
	$foto = mysql_fetch_assoc(mysql_query("SELECT * FROM `gallery_foto` WHERE `id` = '" . $post['id_sim'] . "' LIMIT 1"));
	
	if ($foto['id'])
	{
		?>
		<div class="nav1">
		<img src="/style/icons/camera.png" alt="*"/> <a href="/foto/<?= $avtor['id']?>/<?= $foto['id_gallery']?>/<?= $foto['id']?>/?page=<?= $pageEnd?>"><?= $name?></a> 
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
		<?= $avtor['medal']?> <?= $avtor['online']?> &raquo; <b><?= text($foto['name'])?></b><br />
		<img src="/foto/foto50/<?= $foto['id']?>.<?= $foto['ras']?>" alt="Image" />
		</div>
		<?
	}
	else
	{
		?>
		<div class="mess">
		<?= __('Фотография уже удалена =(')?>
		<span class="time"><?= $s1 . vremja($post['time']) . $s2?></span>
		</div>
		<?
	}

}
?>
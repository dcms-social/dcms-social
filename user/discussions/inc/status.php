<?
/*
* Заголовок обсуждения
*/

if ($type == 'status' && $post['avtor'] != $user['id'])
{
	$name = __('Статус друга');
}
else if ($type == 'status' && $post['avtor'] == $user['id'])
{
	$name = __('Ваш статус');
}
 
/*
* Выводим на экран
*/

if ($type == 'status')
{
	$status = mysql_fetch_assoc(mysql_query("SELECT * FROM `status` WHERE `id` = '" . $post['id_sim'] . "' LIMIT 1"));

	if ($status['id'])
	{
		?>
		<div class="nav1">
		<span class="time"><?= $s1 . vremja($post['time']) . $s2?></span>
		<img src="/style/icons/comment.png" alt="*" /> <a href="/user/status/komm.php?id=<?= $status['id']?>"><?= $name?></a> 
		
		<?
		if ($post['count'] > 0)
		{
			?><b><font color='red'>+<?= $post['count']?></font></b><?
		}
		?>
		</div>
		
		<div class="nav2">
		<b><font color='green'><?= $avtor['nick']?></font></b> 
		<?= ($avtor['id'] != $user['id'] ? '<a href="user.settings.php?id=' . $avtor['id'] . '">[!]</a>' : '')?> 
		<?= $avtor['medal']?> <?= $avtor['online']?> <br />
		
		<div class="st_1"></div>
		<div class="st_2">	
		<span class="text"><?= output_text($status['msg'])?></span><br />
		</div>
		
		<a href="/user/status/komm.php?id=<?= $status['id']?>"><img src="/style/icons/bbl4.png" alt="*" /> 
		<?= mysql_result(mysql_query("SELECT COUNT(*) FROM `status_komm` WHERE `id_status` = '$status[id]'"),0)?></a>
		
		<?
		$l = mysql_result(mysql_query("SELECT COUNT(*) FROM `status_like` WHERE `id_status` = '$status[id]'"),0);
		
		if (isset($user) && $user['id'] != $avtor['id'])
		{
			if ($user['id'] != $avtor['id'] && 
			mysql_result(mysql_query("SELECT COUNT(*) FROM `status_like` WHERE `id_status` = '$status[id]' AND `id_user` = '$user[id]' LIMIT 1"),0) == 0)
			{
				?><a href="?likestatus=<?= $status['id']?>&amp;page=<?= $page?>"><img src="/style/icons/like.gif" alt="*" />Класс!</a> &bull; <?
				$like = $l;
			}
			else
			{
				?><img src="/style/icons/like.gif" alt="*" /> <?= __('Вы и')?> <?
				$like = $l - 1;
			}
		}
		else
		{
			?><img src="/style/icons/like.gif" alt="*" /> <?
			$like = $l;
		}
		
		?>
		<a href="/user/status/like.php?id=<?= $status['id']?>"><?= $like?> <?= __('чел.')?></a>
		</div>
		<?
	}
	else
	{
		?>
		<div class="mess">
		<?= __('Статус уже удален =(')?> 
		<span class="time"><?= $s1 . vremja($post['time']) . $s2?></span>
		</div>
		<?
	}
}
?>
<?
if (user_access('guest_clear'))
{
	if (isset($_POST['write']) && isset($_POST['write2']))
	{
		$timeclear1 = 0;
		if ($_POST['write2'] == 'sut')
		$timeclear1 = $time - intval($_POST['write']) * 60 * 60 * 24;
		
		if ($_POST['write2'] == 'mes')
		$timeclear1 = $time - intval($_POST['write']) * 60 * 60 * 24 * 30;
		
		$q = mysql_query("SELECT * FROM `guest` WHERE `time` < '$timeclear1'", $db);
		
		$del_th = 0;
		
		while ($post = mysql_fetch_assoc($q))
		{
			mysql_query("DELETE FROM `guest` WHERE `id` = '$post[id]'", $db);
			$del_th++;
		}

		admin_log('Гостевая', 'Очистка', 'Удалено ' . $del_th . ' постов');

		mysql_query("OPTIMIZE TABLE `guest`", $db);
		$_SESSION['message'] = 'Удалено ' . $del_th . ' постов';
		header('Location: index.php' . SID);
		exit;
	}
}
?>
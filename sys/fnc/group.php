<?
function group($user = NULL)
{
	global $set, $time;

	if (mysql_result(mysql_query("SELECT COUNT(*) FROM `ban` WHERE `id_user` = '$user' AND (`time` > '$time' OR `navsegda` = '1')"), 0) != 0)
	{
		$ban = ' <img src="/style/user/ban.png" alt="*" class="icon" id="icon_group" /> ';
		return $ban;
	}
	else 
	{

		$ank = mysql_fetch_array(mysql_query("SELECT group_access, pol  FROM `user` WHERE `id` = $user LIMIT 1"));

		if ($ank['group_access'] > 7 && ($ank['group_access'] < 10 || $ank['group_access'] > 14))
		{
			if ($ank['pol'] == 1) $adm = '<img src="/style/user/1.png" alt="*" class="icon" id="icon_group" /> ';
			else
			$adm = '<img src="/style/user/2.png" alt="" class="icon"/> ';
			return $adm;
		}
		elseif (($ank['group_access'] > 1 && $ank['group_access'] <= 7) || ($ank['group_access'] > 10 && $ank['group_access'] <= 14))
		{
			if ($ank['pol'] == 1)
				$mod = '<img src="/style/user/3.png" alt="*" class="icon" id="icon_group" /> ';
			else
				$mod = '<img src="/style/user/4.png" alt="*" class="icon" id="icon_group" /> ';
			return $mod;
		}
		else
		{
			if ($ank['pol'] == 1) 
				$user = '<img src="/style/user/5.png" alt="" class="icon" id="icon_group" /> ';
			else
				$user = '<img src="/style/user/6.png" alt="" class="icon" id="icon_group" /> ';
			return $user;
		}
	}
}
?>
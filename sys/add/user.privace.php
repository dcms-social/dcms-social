<?
/*
==================================
Приватность станички пользователя
При использовании в других модулях
определяйте переменную $ank
::
$ank = get_user(object);
include H.'sys/add/user.privace.php';
==================================
*/	

// Настройки юзера
$uSet = mysql_fetch_array(mysql_query("SELECT * FROM `user_set` WHERE `id_user` = '$ank[id]'  LIMIT 1"));

// Статус друг ли вы
$frend = mysql_result(mysql_query("SELECT COUNT(*) FROM `frends` WHERE 
 (`user` = '$user[id]' AND `frend` = '$ank[id]') OR 
 (`user` = '$ank[id]' AND `frend` = '$user[id]') LIMIT 1"),0);

// Проверка завки в друзья
$frend_new = mysql_result(mysql_query("SELECT COUNT(*) FROM `frends_new` WHERE 
 (`user` = '$user[id]' AND `to` = '$ank[id]') OR 
 (`user` = '$ank[id]' AND `to` = '$user[id]') LIMIT 1"),0);

/*
* Если вы не выше по должности хозяина альбома, 
* и вы не являетесь хозяином альбома
* и ваша должность равна или меньше должности хозяина альбома
* то приватность работает, либо она игнорируется
*/

if ($ank['id'] != $user['id'] && ($user['group_access'] == 0 || $user['group_access'] <= $ank['group_access']))
{	
	// Начинаем вывод если стр имеет приват настройки
	if (($uSet['privat_str'] == 2 && $frend != 2) || $uSet['privat_str'] == 0) 
	{
		if ($ank['group_access'] > 1)
		echo '<div class="err">' . $ank['group_name'] . '</div>';
		
		echo '<div class="nav1">';
		echo group($ank['id']) . user::nick($ank['id'], 0) . medal($ank['id']) . online($ank['id']);
		echo '</div>';		
		
		echo '<div class="nav2">';
		echo avatar($ank['id']);
		echo '</div>';	
	}
	
	if ($uSet['privat_str'] == 2 && $frend != 2) // Если только для друзей
	{
		echo '<div class="mess">';
		echo 'Просматривать страницу пользователя могут только его друзья!';
		echo '</div>';
		
		// В друзья
		if (isset($user))
		{
			echo '<div class="nav1">';
			echo '<img src="/style/icons/druzya.png" alt="*"/>';
			
			if ($frend_new == 0 && $frend==0)
			{
				echo '<a href="/user/frends/create.php?add=' . $ank['id'] . '">Добавить в друзья</a><br />';
			}
			elseif ($frend_new == 1)
			{
				echo '<a href="/user/frends/create.php?otm=' . $ank['id'] . '">Отклонить заявку</a><br />';
			}
			elseif ($frend == 2)
			{
				echo '<a href="/user/frends/create.php?del=' . $ank['id'] . '">Удалить из друзей</a><br />';
			}
			
			echo '</div>';
		}
		include_once H.'sys/inc/tfoot.php';
		exit;
	}
	
	// Если cтраница закрыта
	if ($uSet['privat_str'] == 0) 
	{
		echo '<div class="mess">';
		echo 'Пользователь полностью ограничил доступ к своей странице!';
		echo '</div>';
		
		include_once H.'sys/inc/tfoot.php';
		exit;
	}
}
?>
<?
/*-----------------------статус форма-----------------------*/
if (isset($user) && isset($_GET['status']))
{
	if ($user['id'] == $ank['id'])
	{
		echo '<div class="main">Статус [512 символов]</div>';
		echo '<form action="/info.php?id=' . $ank['id'] . '" method="post">';
		echo "$tPanel<textarea type=\"text\" style='' name=\"status\" value=\"\"/></textarea><br /> ";
		echo "<input class=\"submit\" style='' type=\"submit\" value=\"Установить\" />";
		echo " <a href='/info.php?id=$ank[id]'>Отмена</a><br />";
		echo "</form>";
		include_once 'sys/inc/tfoot.php';
		exit;
	}
}
/*-----------------------------------------------------------*/


if ($ank['group_access']>1)echo "<div class='err'>$ank[group_name]</div>";

echo "<div class='nav1'>";
echo group($ank['id']) . " $ank[nick] ";
echo medal($ank['id']) . " " . online($ank['id']) . " ";

if ((user_access('user_ban_set') || user_access('user_ban_set_h') || user_access('user_ban_unset')) && $ank['id'] != $user['id'])
echo "<a href='/adm_panel/ban.php?id=$ank[id]'><font color=red>[Бан]</font></a>";
echo "</div>";

// Аватар
echo "<div class='nav2'>";
echo avatar($ank['id'], true, 128, false);
echo "<br />";

if (isset($user) && isset($_GET['like']) && $user['id']!=$ank['id'] && mysql_result(mysql_query("SELECT COUNT(*) FROM `status_like` WHERE `id_status` = '$status[id]' AND `id_user` = '$user[id]' LIMIT 1"),0)==0){
mysql_query("INSERT INTO `status_like` (`id_user`, `id_status`) values('$user[id]', '$status[id]')");
}
if ($status['id'] || $ank['id'] == $user['id'])
{
echo "<div class='st_1'></div>";
echo "<div class='st_2'>";
if ($status['id'])
{
echo output_text($status['msg']) . ' <font style="font-size:11px; color:gray;">' . vremja($status['time']) . '</font>';
if ($ank['id']==$user['id'])echo " [<a href='?id=$ank[id]&amp;status'><img src='/style/icons/edit.gif' alt='*'> нов</a>]";
echo '<br />';
}
else if ($ank['id']==$user['id'])
{
echo "Ваш статус [<a href='?id=$ank[id]&status'><img src='/style/icons/edit.gif' alt='*'> ред</a>]";
}
echo "</div>";

 // Если статус установлен
if ($status['id'])
{
	echo " <a href='/user/status/komm.php?id=$status[id]'><img src='/style/icons/bbl4.png' alt=''/> " . mysql_result(mysql_query("SELECT COUNT(*) FROM `status_komm` WHERE `id_status` = '$status[id]'"),0) . " </a> ";
	$l=mysql_result(mysql_query("SELECT COUNT(*) FROM `status_like` WHERE `id_status` = '$status[id]'"),0);if (isset($user) && $user['id']!=$ank['id'] && mysql_result(mysql_query("SELECT COUNT(*) FROM `status_like` WHERE `id_status` = '$status[id]' AND `id_user` = '$user[id]' LIMIT 1"),0)==0)
{	

	echo " <a href='/info.php?id=$ank[id]&amp;like'><img src='/style/icons/like.gif' alt='*'/> Класс!</a> • ";
	$like = $l;
}
else if(isset($user) && $user['id']!=$ank['id'])
{
	echo " <img src='/style/icons/like.gif' alt=''/> Вы и ";
	$like = $l-1;
}
else
{
	echo " <img src='/style/icons/like.gif' alt=''/> ";
	$like = $l;
} 

	echo "<a href='/user/status/like.php?id=$status[id]'> $like чел. </a>";
	

}

 /* Общее колличество статусов */
$st = mysql_result(mysql_query("SELECT COUNT(*) FROM `status` WHERE `id_user` = '$ank[id]'"),0);

if ($st > 0)
{
	echo "<br /> &rarr; <a href='/user/status/index.php?id=$ank[id]'>Все статусы</a> (" . $st . ")";
}

}

echo "</div>";

  
/*
========================================
Подарки
========================================
*/  
$k_p = mysql_result(mysql_query("SELECT COUNT(id) FROM `gifts_user` WHERE `id_user` = '$ank[id]' AND `status` = '1'"),0);

$width = ($webbrowser == 'web' ? '60' : '45'); // Размер подарков при выводе в браузер

if ($k_p > 0)
{	
	$q = mysql_query("SELECT id,id_gift,status FROM `gifts_user` WHERE `id_user` = '$ank[id]' AND `status` = '1' ORDER BY `id` DESC LIMIT 5");
	echo '<div class="nav2">';
	while ($post = mysql_fetch_assoc($q))
	{
		$gift = mysql_fetch_assoc(mysql_query("SELECT id FROM `gift_list` WHERE `id` = '$post[id_gift]' LIMIT 1"));
		echo '<a href="/user/gift/gift.php?id=' . $post['id'] . '"><img src="/sys/gift/' . $gift['id'] . '.png" style="max-width:' . $width . 'px;" alt="Подарок" /></a> ';
	}
	echo '</div>';
	
	echo '<div class="nav2">';
	echo '&rarr; <a href="/user/gift/index.php?id=' . $ank['id'] . '">Все подарки</a> (' . $k_p . ')';
	echo '</div>';
}
 
/*
========================================
Анкета
========================================
*/
echo "<div class='nav1'>";
echo "<img src='/style/icons/anketa.gif' alt='*' /> <a href='/user/info/anketa.php?id=$ank[id]'>Анкета</a> ";
if (isset($user) && $user['id']==$ank['id'])
{
	echo "[<img src='/style/icons/edit.gif' alt='*' /> <a href='/user/info/edit.php'>ред</a>]";
}
echo "</div>";
/*
========================================
Гости
========================================
*/
if (isset($user) && $user['id'] == $ank['id'])
{
	echo '<div class="nav2">';
	
	$new_g = mysql_result(mysql_query("SELECT COUNT(*) FROM `my_guests` WHERE `id_ank` = '$user[id]' AND `read`='1'"),0);
	
	echo '<img src="/style/icons/guests.gif" alt="*" /> ';
	
	if($new_g != 0)
	{
		echo "<a href='/user/myguest/index.php'><font color='red'>Гости +$new_g</font></a> ";
	}
	else
	{
		echo "<a href='/user/myguest/index.php'>Гости</a> ";
	}

	echo ' | ';

	$ocenky = mysql_result(mysql_query("SELECT COUNT(*) FROM `gallery_rating` WHERE `avtor` = '$ank[id]'  AND `read`='1'"),0);
	
	if($ocenky != 0)
	{
		echo "<a href='/user/info/ocenky.php'><font color='red'>Оценки +$ocenky</font></a> ";
	}
	else
	{
		echo "<a href='/user/info/ocenky.php'>Оценки</a> ";
	}
	echo "</div>";
}
/*
========================================
Друзья
========================================
*/
$k_f = mysql_result(mysql_query("SELECT COUNT(id) FROM `frends_new` WHERE `to` = '$ank[id]' LIMIT 1"), 0);
$k_fr = mysql_result(mysql_query("SELECT COUNT(*) FROM `frends` WHERE `user` = '$ank[id]' AND `i` = '1'"), 0);
$res = mysql_query("select `frend` from `frends` WHERE `user` = '$ank[id]' AND `i` = '1'");

echo '<div class="nav2">';
echo '<img src="/style/icons/druzya.png" alt="*" /> ';
echo '<a href="/user/frends/?id=' . $ank['id'] . '">Друзья</a> (' . $k_fr . '</b>/';

$i = 0;

while ($k_fr = mysql_fetch_array($res))
{
	if (mysql_result(mysql_query("SELECT COUNT(*) FROM `user` WHERE `id` = '$k_fr[frend]' && `date_last` > '".(time()-600)."'"),0) != 0) 
	$i++;
}

echo "<span style='color:green'><a href='/user/frends/online.php?id=".$ank['id']."'>$i</a></span>)";
if ($k_f>0 && $ank['id'] == $user['id'])echo " <a href='/user/frends/new.php'><font color='red'>+$k_f</font></a>";
echo "</div>";



if (isset($user) && $user['id'] == $ank['id'])
{
echo "<div class='nav2'>";
/*
========================================
Уведомления
========================================
*/
if (isset($user) && $user['id']==$ank['id']){
	
	$k_notif = mysql_result(mysql_query("SELECT COUNT(`read`) FROM `notification` WHERE `id_user` = '$user[id]' AND `read` = '0'"), 0); // Уведомления
		
		if($k_notif > 0)
		{
			echo "<img src='/style/icons/notif.png' alt='*' /> ";
			echo "<a href='/user/notification/index.php'><font color='red'>Уведомления</font></a> ";
			echo "<font color=\"red\">+$k_notif</font> ";
			echo "<br />";
		}

}

/*
========================================
Обсуждения
========================================
*/
if (isset($user) && $user['id']==$ank['id']){

	echo "<img src='/style/icons/chat.gif' alt='*' /> ";
	$new_g=mysql_result(mysql_query("SELECT COUNT(*) FROM `discussions` WHERE `id_user` = '$user[id]' AND `count` > '0'"),0);
		if($new_g!=0)
		{
			echo "<a href='/user/discussions/index.php'><font color='red'>Обсуждения</font></a> ";
			echo "<font color=\"red\">+$new_g</font> ";
		}else{
			echo "<a href='/user/discussions/index.php'>Обсуждения</a> ";
		}
	echo "<br />";
}

/*
========================================
Лента
========================================
*/
if ($user['id']==$ank['id'])
{
$k_l=mysql_result(mysql_query("SELECT COUNT(*) FROM `tape` WHERE `id_user` = '$user[id]'  AND  `read` = '0'"),0);
if($k_l!=0)
{
	$color = "<font color='red'>";
	$color2 = "</font>";
}
else
{
	$color = null;
	$color2 = null;
}
echo "<img src='/style/icons/lenta.gif' alt='*' /> <a href='/user/tape/'>".$color."Лента".$color2."</a> ";
if($k_l!=0)echo "<font color=\"red\">+$k_l</font>";
echo "<br />";
}

echo "</div>";
}

echo "<div class='nav1'>";
/*
========================================
Фото
========================================
*/
echo "<img src='/style/icons/foto.png' alt='*' /> ";
echo "<a href='/foto/$ank[id]/'>Фотографии</a> ";
echo "(" . mysql_result(mysql_query("SELECT COUNT(*) FROM `gallery_foto` WHERE `id_user` = '$ank[id]'"),0) . ")<br />";

/*
========================================
Файлы
========================================
*/
if (mysql_result(mysql_query("SELECT COUNT(*) FROM `user_files` WHERE `id_user` = '$ank[id]' AND `osn` = '1'"), 0)==0)
{
mysql_query("INSERT INTO `user_files` (`id_user`, `name`,  `osn`) values('$ank[id]', 'Файлы', '1')");
}
$dir_osn = mysql_fetch_assoc(mysql_query("SELECT * FROM `user_files` WHERE `id_user` = '$ank[id]' AND `osn` = '1' LIMIT 1"));

echo "<img src='/style/icons/files.gif' alt='*' /> ";
echo "<a href='/user/personalfiles/$ank[id]/$dir_osn[id]/'>Файлы</a> ";
echo "(" . mysql_result(mysql_query("SELECT COUNT(*) FROM `user_files` WHERE `id_user` = '$ank[id]' AND `osn` > '1'"),0) . "/" . mysql_result(mysql_query("SELECT COUNT(*) FROM `obmennik_files` WHERE `id_user` = '$ank[id]'"),0) . ")<br />";

/*
========================================
Музыка
========================================
*/
$k_music=mysql_result(mysql_query("SELECT COUNT(*) FROM `user_music` WHERE `id_user` = '$ank[id]'"),0);

echo "<img src='/style/icons/play.png' alt='*' width='16'/> ";
echo "<a href='/user/music/index.php?id=$ank[id]'>Музыка</a> ";
echo "(" . $k_music . ")";

echo "</div>";
/*
========================================
Темы и комментарии
========================================
*/
echo "<div class='nav2'><img src='/style/icons/blogi.png' alt='*' width='16'/> ";
echo "<a href='/user/info/them_p.php?id=".$ank['id']."'>Темы и комментарии</a> ";

echo "</div>";
/*
========================================
Дневники
========================================
*/
echo "<div class='nav2'>";
$kol_dnev=mysql_result(mysql_query("SELECT COUNT(*) FROM `notes` WHERE `id_user` = '".$ank['id']."'"),0);
echo "<img src='/style/icons/zametki.gif' alt='*' /> ";
echo "<a href='/plugins/notes/user.php?id=$ank[id]'>Дневники</a> ($kol_dnev)<br />"; 

/*
========================================
Закладки
========================================
*/

$zakladki =mysql_result(mysql_query("SELECT COUNT(`id`)FROM `bookmarks` WHERE `id_user`='".$ank['id']."'"),0);;

echo "<img src='/style/icons/fav.gif' alt='*' /> ";
echo "<a href='/user/bookmark/index.php?id=$ank[id]'>Закладки</a> ($zakladki)<br />";

/*
========================================
Отзывы
========================================
*/

echo "<img src='/style/my_menu/who_rating.png' alt='*' /> <a href='/user/info/who_rating.php?id=$ank[id]'>Отзывы</a>
 (".mysql_result(mysql_query("SELECT COUNT(*) FROM `user_voice2` WHERE `id_kont` = '".$ank['id']."'"),0).")<br />";
 echo "</div>";
/*
========================================
Сообщение
========================================
*/
if (isset($user) && $ank['id']!=$user['id']){
	echo "<div class='nav1'>";
	echo " <a href=\"/mail.php?id=$ank[id]\"><img src='/style/icons/pochta.gif' alt='*' /> Сообщение</a><br />";/*
========================================
В друзья
========================================
*/

if ($frend_new==0 && $frend==0){
echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?add=".$ank['id']."'>Добавить в друзья</a><br />";
}elseif ($frend_new==1){
echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?otm=$ank[id]'>Отклонить заявку</a><br />";
}elseif ($frend==2){
echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?del=$ank[id]'>Удалить из друзей</a><br />";
}
/*
========================================
В закладки
========================================
*/

	echo '<img src="/style/icons/fav.gif" alt="*" /> ';
	if ( mysql_result(mysql_query("SELECT COUNT(*) FROM `mark_people` WHERE `id_user` = '" . $user['id'] . "' AND `id_people` = '" . $ank['id'] . "' LIMIT 1"),0) == 0)
		echo '<a href="?id=' . $ank['id'] . '&amp;fav=1">В закладки</a><br />';
	else
		echo '<a href="?id=' . $ank['id'] . '&amp;fav=0">Удалить из закладок</a><br />';
echo "</div>";

echo "<div class='nav2'>";
/*
========================================
Монеты перевод
========================================
*/
echo "<img src='/style/icons/uslugi.gif' alt='*' /> <a href=\"/user/money/translate.php?id=$ank[id]\">Перевести $sMonet[0]</a><br />";

/*
========================================
Сделать подарок
========================================
*/
echo "<img src='/style/icons/present.gif' alt='*' /> <a href=\"/user/gift/categories.php?id=$ank[id]\">Сделать подарок</a><br />";echo "</div>";
}

/*
========================================
Настройки
========================================
*/

if (isset($user) && $ank['id']==$user['id']){

echo "<div class='main'>";
echo "<img src='/style/icons/uslugi.gif' alt='*' /> <a href=\"/user/money/index.php\">Дополнительные услуги</a><br /> ";

echo "<img src='/style/icons/settings.png' alt='*' /> <a href=\"/user/info/settings.php\">Мои настройки</a> | <a href=\"/umenu.php\">Меню</a>";
echo "</div>";
}

/*
========================================
Стена
========================================
*/
echo "<div class='foot'>";
echo "<img src='/style/icons/stena.gif' alt='*' /> ";
if (isset($user) && $user['wall']==0)
echo "<a href='/info.php?id=$ank[id]&amp;wall=1'>Стена</a>";
elseif (isset($user))
echo "<a href='/info.php?id=$ank[id]&amp;wall=0'>Стена</a>";
else
echo "Стена";
echo "</div>";
if ($user['wall']==0){
include_once H.'user/stena/index.php';
}
/*
========================================
The End
========================================
*/

?>
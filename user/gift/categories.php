<?
/*
=======================================
Подарки для Dcms-Social
Автор: Искатель
---------------------------------------
Этот скрипт распостроняется по лицензии
движка Dcms-Social. 
При использовании указывать ссылку на
оф. сайт http://dcms-social.ru
---------------------------------------
Контакты
ICQ: 587863132
http://dcms-social.ru
=======================================
*/
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/user.php';

only_reg();


$width = ($webbrowser == 'web' ? '100' : '70'); // Размер подарков при выводе в браузер

if (isset($_GET['id']))$ank['id'] = intval($_GET['id']);
$ank = get_user($ank['id']);
if(!$ank || $ank['id'] == 0 || $ank['id'] == $user['id']){ header("Location: /index.php?".SID); exit; }

	$set['title']="Подарок для $ank[nick]";
	include_once '../../sys/inc/thead.php';
	title();
	aut();
	
/*
==================================
Дарим подарок
==================================
*/
if (isset($_GET['gift']) && isset($_GET['category']))
{
// Категория
$category = mysql_fetch_assoc(mysql_query("SELECT * FROM `gift_categories` WHERE `id` = '" . intval($_GET['category']) . "' LIMIT 1"));

// Подарок
$gift = mysql_fetch_assoc(mysql_query("SELECT * FROM `gift_list` WHERE `id` = '" . intval($_GET['gift']) . "' LIMIT 1"));

	if (isset($_GET['ok']) )
	{
		if ($user['money'] >= $gift['money'])
		{	
		
			$msg = my_esc($_POST['msg']);  // Комментарий
			
			mysql_query("UPDATE `user` SET `money` = '" . ($user['money'] - $gift['money']) . "' WHERE `id` = '$user[id]'");
			
			mysql_query("INSERT INTO `gifts_user` (`id_user`, `id_ank`, `id_gift`, `coment`, `time`) values('$ank[id]', '$user[id]', '$gift[id]', '$msg', '$time')");
			
			$id_gift = mysql_insert_id();
		/*
		==========================
		Уведомления о подарках
		==========================
		*/
		mysql_query("INSERT INTO `notification` (`avtor`, `id_user`, `id_object`, `type`, `time`) VALUES ('$user[id]', '$ank[id]', '$id_gift', 'new_gift', '$time')");
		
			
			$_SESSION['message'] = 'Ваш подарок успешно отправлен';
			header("Location: /info.php?id=$ank[id]");
			exit;
			
		}else{
		
		$err = 'У вас не достаточно средств на счету';
		
		}
		
	}

	err(); 

	echo '<div class="foot">';
	echo '<img src="/style/icons/str2.gif" alt="*" />  <a href="?id=' . $ank['id'] . '">Категории</a> |  <a href="?category=' . $category['id'] . '&amp;id=' . $ank['id'] . '">' . htmlspecialchars($category['name']) . '</a> | <b>' . htmlspecialchars($gift['name']) . '</b><br />';	
	echo '</div>';

	echo '<form action="?category=' . $category['id'] . '&amp;gift=' . $gift['id'] . '&amp;id=' . $ank['id'] . '&amp;ok" method="post">';
	
	echo '<div class="mess">';
	echo 'Подарок <img src="/sys/gift/' . $gift['id'] . '.png" style="max-width:' . $width . 'px;" alt="*" /> для ';
	echo status($ank['id']) , group($ank['id']) , $ank['nick'] , medal($ank['id']) , online($ank['id']) . '<br />';
	echo 'Стоимость <b><font color=red>' . intval($gift['money']) . '</font> <font color=green>' . $sMonet[0] . '</font></b> у вас <b><font color=red>' . $user['money'] . '</font>  <font color=green>' . $sMonet[0] . '</font></b><br />';


	echo '</div>'; 
	
	
	echo '<div class="mess">';
	echo $tPanel . '<textarea type="text" name="msg" value=""/></textarea><br />';
	
	echo '<input class="submit" type="submit" value="Подарить" /> ';
	echo '<img src="/style/icons/delete.gif" alt="*" /> <a href="/info.php?id=' . $ank['id'] . '">Отмена</a> ';
	echo '</div>';
	
	echo "</form>";
	
	echo '<div class="foot">';
	echo '<img src="/style/icons/str2.gif" alt="*" />  <a href="?id=' . $ank['id'] . '">Категории</a> |  <a href="?category=' . $category['id'] . '&amp;id=' . $ank['id'] . '">' . htmlspecialchars($category['name']) . '</a> | <b>' . htmlspecialchars($gift['name']) . '</b><br />';	
	echo '</div>';
	
}

else
	
/*
==================================
Вывод подарков
==================================
*/
if (isset($_GET['category']))
{

// Категория
$category = mysql_fetch_assoc(mysql_query("SELECT * FROM `gift_categories` WHERE `id` = '" . intval($_GET['category']) . "' LIMIT 1"));


if (!$category) 
{  
	$_SESSION['message'] = 'Нет такой категории';
	header("Location: ?");
	exit;
}

echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*" />  <a href="?id=' . $ank['id'] . '">Категории</a> | <b>' . htmlspecialchars($category['name']) . '</b><br />';	
echo '</div>';
	
// Список подарков

	
$k_post = mysql_result(mysql_query("SELECT COUNT(id) FROM `gift_list` WHERE `id_category` = '$category[id]'"),0);

if ($k_post == 0)
{
	echo '<div class="mess">';
	echo 'Нет подарков';
	echo '</div>';
}

$k_page=k_page($k_post,$set['p_str']);
$page=page($k_page);
$start=$set['p_str']*$page-$set['p_str'];
$q = mysql_query("SELECT name,id,money FROM `gift_list` WHERE `id_category` = '$category[id]' ORDER BY `id` LIMIT $start, $set[p_str]");

while ($post = mysql_fetch_assoc($q))
{

/*-----------зебра-----------*/ 
	if ($num==0){
		echo '<div class="nav1">';
		$num=1;
	}
	elseif ($num==1){
		echo '<div class="nav2">';
		$num=0;
	}
/*---------------------------*/

echo '<img src="/sys/gift/' . $post['id'] . '.png" style="max-width:' . $width . 'px;" alt="*" /><br />';
echo '<a href="?category=' . $category['id'] . '&amp;gift=' . $post['id'] . '&amp;id=' . $ank['id'] . '"><b>' . htmlspecialchars($post['name']) . '</b></a> :: ';
echo '<b><font color=red>' . intval($post['money']) . '</font> <font color=green>' . $sMonet[0] . '</font></b>';

echo '</div>';
}

if ($k_page>1)str('categories.php?id=' . intval($_GET['id']) . '&amp;category=' . intval($_GET['category']) . '&amp;',$k_page,$page); // Вывод страниц

echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*" />  <a href="?id=' . $ank['id'] . '">Категории</a> | <b>' . htmlspecialchars($category['name']) . '</b><br />';	
echo '</div>';


}

else
/*
==================================
Вывод категорий
==================================
*/
{
echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*" /> <a href="/info.php?id=' . $ank['id'] . '">' . $ank['nick'] . '</a> | <b>Категории</b>';
echo '</div>';

$k_post = mysql_result(mysql_query("SELECT COUNT(id) FROM `gift_categories`"),0);

if ($k_post == 0)
{
	echo '<div class="mess">';
	echo 'Нет категорий';
	echo '</div>';
}

$q = mysql_query("SELECT name,id FROM `gift_categories` ORDER BY `id`");

while ($post = mysql_fetch_assoc($q))
{

/*-----------зебра-----------*/ 
	if ($num==0){
		echo '<div class="nav1">';
		$num=1;
	}
	elseif ($num==1){
		echo '<div class="nav2">';
		$num=0;
	}
/*---------------------------*/

echo '<img src="/style/themes/default/loads/14/dir.png" alt="*" /> <a href="categories.php?category=' . $post['id'] . '&amp;id=' . $ank['id'] . '">' . htmlspecialchars($post['name']) . '</a> ';

echo '(' . mysql_result(mysql_query("SELECT COUNT(id) FROM `gift_list` WHERE `id_category` = '$post[id]'"),0) . ')';

echo '</div>';

}

echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*" /> <a href="/info.php?id=' . $ank['id'] . '">' . $ank['nick'] . '</a> | <b>Категории</b>';
echo '</div>';
}
include_once '../../sys/inc/tfoot.php';
?>
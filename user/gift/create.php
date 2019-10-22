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
only_level(3);

$width = ($webbrowser == 'web' ? '100' : '70'); // Размер подарков при выводе в браузер

/*
==================================
Редактирование подарков
==================================
*/
if (isset($_GET['edit_gift']) && isset($_GET['category']))
{

$category = mysql_fetch_assoc(mysql_query("SELECT * FROM `gift_categories` WHERE `id` = '" . intval($_GET['category']) . "' LIMIT 1"));
$gift = mysql_fetch_assoc(mysql_query("SELECT * FROM `gift_list` WHERE `id` = '" . intval($_GET['edit_gift']) . "' LIMIT 1"));


if (!$category || !$gift) 
{  
	$_SESSION['message'] = 'Нет такой категории или подарка';
	header("Location: ?");
	exit;
}

	if (isset($_POST['name']) && isset($_POST['money'])) // Редактирование записи
	{
		$name = my_esc($_POST['name']);
		$money = intval($_POST['money']);
		
		if ($money < 1)$err = 'Укажите стоимость подарка';
		
		if (strlen2($name) < 2)$err = 'Короткое название';
		if (strlen2($name) > 128)$err = 'Длина названия превышает предел в 128 символов';
		
		if (!isset($err))
		{
			mysql_query("UPDATE `gift_list` SET `name` = '$name' , `money` = '$money', `id_category` = '$category[id]' WHERE `id` = '$gift[id]'");
			
			$_SESSION['message'] = 'Подарок успешно отредактирован';
			header('Location: ?category=' . $category['id'] . '&page=' . intval($_GET['page']));
			exit;
		}
	}
	
	if (isset($_GET['delete'])) // Удаление подарка
	{

		
		unlink(H.'sys/gift/' . $gift['id'] . '.png');
		
		mysql_query("DELETE FROM `gift_list` WHERE `id` = '$gift[id]'");
		mysql_query("DELETE FROM `gifts_user` WHERE `id_gift` = '$gift[id]'");
		
		$_SESSION['message'] = 'Подарок успешно удален';
		
		header("Location: ?category=$category[id]&page=" . intval($_GET['page']));
		exit;
	}

	$set['title'] = 'Редактирование подарка';
	include_once '../../sys/inc/thead.php';
	title();
	aut();
	err();
	
	echo '<div class="foot">';
	echo '<img src="/style/icons/str2.gif" alt="*" />  <a href="?">Категории</a> |  <a href="?category=' . $category['id'] . '">' . htmlspecialchars($category['name']) . '</a> | <b>Добавление подарка</b><br />';	
	echo '</div>';

// Форма редактирования подарка

	echo '<form class="main" method="post" enctype="multipart/form-data"  action="?category=' . $category['id'] . '&amp;edit_gift=' . $gift['id'] . '&amp;page=' . intval($_GET['page']) . '">';
	echo '<img src="/sys/gift/' . $gift['id'] . '.png" style="max-width:' . $width . 'px;" alt="*" /><br />';
	echo 'Название:<br /><input type="text" name="name" value="' . htmlspecialchars($gift['name']) . '" /><br />';
	echo 'Цена:<br /><input type="text" name="money" value="' . $gift['money'] . '" style="width:30px;"/><br />';
	echo '<input value="Сохранить" type="submit" />';
	echo '</form>';
	
	echo '<div class="foot">';
	echo '<img src="/style/icons/str2.gif" alt="*" />  <a href="?">Категории</a> |  <a href="?category=' . $category['id'] . '">' . htmlspecialchars($category['name']) . '</a> | <b>Добавление подарка</b><br />';	
	echo '</div>';	
}

else 

/*
==================================
Добавление подарков
==================================
*/
if (isset($_GET['add_gift']) && isset($_GET['category']))
{

$category = mysql_fetch_assoc(mysql_query("SELECT * FROM `gift_categories` WHERE `id` = '" . intval($_GET['category']) . "' LIMIT 1"));


if (!$category) 
{  
	$_SESSION['message'] = 'Нет такой категории';
	header("Location: ?");
	exit;
}

	if (isset($_POST['name']) && isset($_POST['money']) && isset($_FILES['gift'])) // Создание записи
	{
		$name = my_esc($_POST['name']);
		$money = intval($_POST['money']);
		
		if ($money < 1)$err = 'Укажите стоимость подарка';
		
		if (strlen2($name) < 2)$err = 'Короткое название';
		if (strlen2($name) > 128)$err = 'Длина названия превышает предел в 128 символов';
		
		if (!isset($err))
		{
			mysql_query("INSERT INTO `gift_list` (`name`, `money`, `id_category`) values('$name', '$money', '$category[id]')");
			
			$file_id = mysql_insert_id();
			
			copy($_FILES['gift']['tmp_name'], H.'sys/gift/' . $file_id . '.png');
			@chmod(H.'sys/gift/' . $file_id . '.png' , 0777);
			
			$_SESSION['message'] = 'Подарок успешно добавлен';
			header("Location: ?category=" . $category['id']);
			exit;
		}
	}

	$set['title'] = 'Добавление подарка';
	include_once '../../sys/inc/thead.php';
	title();
	aut();
	err();

	echo '<div class="foot">';
	echo '<img src="/style/icons/str2.gif" alt="*" />  <a href="?">Категории</a> |  <a href="?category=' . $category['id'] . '">' . htmlspecialchars($category['name']) . '</a> | <b>Добавление подарка</b><br />';	
	echo '</div>';

// Форма создания категории

	echo '<form class="main" method="post" enctype="multipart/form-data"  action="?category=' . $category['id'] . '&amp;add_gift">';
	echo 'Название:<br /><input type="text" name="name" value="" /><br />';
	echo 'Цена:<br /><input type="text" name="money" value="" style="width:30px;"/><br />';
	echo 'Подарок:<br /><input name="gift" accept="image/*,image/png" type="file" /><br />';
	echo '<input value="Добавить" type="submit" />';
	echo '</form>';
	
	echo '<div class="foot">';
	echo '<img src="/style/icons/str2.gif" alt="*" />  <a href="?">Категории</a> |  <a href="?category=' . $category['id'] . '">' . htmlspecialchars($category['name']) . '</a> | <b>Добавление подарка</b><br />';	
	echo '</div>';
}

else

/*
==================================
Вывод подарков
==================================
*/

if (isset($_GET['category'])){

$category = mysql_fetch_assoc(mysql_query("SELECT * FROM `gift_categories` WHERE `id` = '" . intval($_GET['category']) . "' LIMIT 1"));


if (!$category) 
{  
	$_SESSION['message'] = 'Нет такой категории';
	header("Location: ?");
	exit;
}


	$set['title'] = 'Список подарков';
	include_once '../../sys/inc/thead.php';
	title();
	aut();
	err();

echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*" />  <a href="?">Категории</a> | <b>' . htmlspecialchars($category['name']) . '</b><br />';	
echo '</div>';
	
// Список подарков

	
$k_post = mysql_result(mysql_query("SELECT COUNT(id) FROM `gift_list`  WHERE `id_category` = '$category[id]'"),0);

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
echo 'Название: ' . htmlspecialchars($post['name']) . '<br /> ';
echo 'Стоимость: ' . $post['money'] . ' ' . $sMonet[0];
echo ' <a href="create.php?category=' . $category['id'] . '&amp;edit_gift=' . $post['id'] . '&amp;page=' . $page . '"><img src="/style/icons/edit.gif" alt="*" /></a> ';
echo ' <a href="create.php?category=' . $category['id'] . '&amp;edit_gift=' . $post['id'] . '&amp;page=' . $page . '&amp;delete"><img src="/style/icons/delete.gif" alt="*" /></a> ';


echo '</div>';
}

if ($k_page>1)str('create.php?category=' . intval($_GET['category']) . '&amp;',$k_page,$page); // Вывод страниц

echo '<div class="foot">';
echo '<img src="/style/icons/ok.gif" alt="*" />  <a href="?category=' . $category['id'] . '&amp;add_gift">Добавить подарок</a><br />';	
echo '</div>';

echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*" />  <a href="?">Категории</a> | <b>' . htmlspecialchars($category['name']) . '</b><br />';	
echo '</div>';

}

else

/*
==================================
Создание категорий
==================================
*/
if (isset($_GET['add_category']))
{

	if (isset($_POST['name']) && $_POST['name'] != NULL) // Создание записи
	{
		$name = my_esc($_POST['name']);
		
		if (strlen2($name) < 2)$err='Короткое название';
		if (strlen2($name) > 128)$err='Длина названия превышает предел в 128 символов';
		
		if (!isset($err))
		{
			mysql_query("INSERT INTO `gift_categories` (`name`) values('$name')");
			
			$_SESSION['message'] = 'Категория успешно добавлена';
			header("Location: ?");
			exit;
		}
	}

	$set['title'] = 'Создание категорий';
	include_once '../../sys/inc/thead.php';
	title();
	aut();
	err();



	echo '<div class="foot">';
	echo '<img src="/style/icons/str2.gif" alt="*" />  <a href="?">Категории</a><br />';	
	echo '</div>';
	
	// Форма создания категории
	echo '<form class="main" method="post" action="?add_category">';
	echo 'Название:<br /><input type="text" name="name" value="" /><br />';
	echo '<input value="Добавить" type="submit" />';
	echo '</form>';
	
	echo '<div class="foot">';
	echo '<img src="/style/icons/str2.gif" alt="*" />  <a href="?">Категории</a><br />';	
	echo '</div>';
}

else

/*
==================================
Редактирование категорий
==================================
*/
if (isset($_GET['edit_category']))
{

$category = mysql_fetch_assoc(mysql_query("SELECT * FROM `gift_categories` WHERE `id` = '" . intval($_GET['edit_category']) . "' LIMIT 1"));

if (!$category) 
{  
	$_SESSION['message'] = 'Нет такой категории';
	header("Location: ?");
	exit;
}


	if (isset($_POST['name']) && $_POST['name'] != NULL) // Создание записи
	{
		$name = my_esc($_POST['name']);
		
		if (strlen2($name) < 2)$err='Короткое название';
		if (strlen2($name) > 128)$err='Длина названия превышает предел в 128 символов';
		
		if (!isset($err))
		{
			mysql_query("UPDATE `gift_categories` SET `name` = '$name' WHERE `id` = '$category[id]'");
			
			$_SESSION['message'] = 'Категория успешно переименована';
			header("Location: ?");
			exit;
		}
	}
	
	if (isset($_GET['delete'])) // Удаление категории
	{

		$q = mysql_query("SELECT id FROM `gift_list` WHERE `id_category` = '$category[id]'");

		while ($post = mysql_fetch_assoc($q))
		{
		unlink(H.'sys/gift/' . $post['id'] . '.png');
		mysql_query("DELETE FROM `gifts_user` WHERE `id_gift` = '$post[id]'");
		}
		
		mysql_query("DELETE FROM `gift_list` WHERE `id_category` = '$category[id]'");
		
		
		mysql_query("DELETE FROM `gift_categories` WHERE `id` = '$category[id]' LIMIT 1");	
		
		$_SESSION['message'] = 'Категория успешно удалена';
		
		header("Location: ?");
		exit;
	}
	
	
	$set['title'] = 'Редактирование категории';
	include_once '../../sys/inc/thead.php';
	title();
	aut();
	err();

	// Форма редактирования категории

	echo '<form class="main" method="post" action="?edit_category=' . $category['id'] . '">';
	echo 'Название:<br /><input type="text" name="name" value="' . htmlspecialchars($category['name']) . '" /><br />';
	echo '<input value="Добавить" type="submit" />';
	echo '</form>';

}


else


/*
==================================
Вывод категорий
==================================
*/

{

	$set['title'] = 'Список категорий';
	include_once '../../sys/inc/thead.php';
	title();
	aut();
	err();

	
// Список категорий	

	
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

echo '<img src="/style/themes/default/loads/14/dir.png" alt="*" /> <a href="create.php?category=' . $post['id'] . '">' . htmlspecialchars($post['name']) . '</a> ';
echo '(' . mysql_result(mysql_query("SELECT COUNT(id) FROM `gift_list` WHERE `id_category` = '$post[id]'"),0) . ')';
echo ' <a href="create.php?edit_category=' . $post['id'] . '"><img src="/style/icons/edit.gif" alt="*" /></a> ';
echo ' <a href="create.php?edit_category=' . $post['id'] . '&amp;delete"><img src="/style/icons/delete.gif" alt="*" /></a> ';


echo '</div>';
}

echo '<div class="foot">';
echo '<img src="/style/icons/ok.gif" alt="*" />  <a href="?add_category">Создать категорию</a><br />';	
echo '</div>';


}
include_once '../../sys/inc/tfoot.php';

?>
<?
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';

// Заголовок
$set['title'] = 'Новости';

include_once '../sys/inc/thead.php';
title();
aut(); 

// Колличество новостей
$k_post = mysql_result(mysql_query("SELECT COUNT(*) FROM `news`"),0);


$k_page = k_page($k_post,$set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];

// Выборка новостей
$q = mysql_query("SELECT * FROM `news` ORDER BY `id` DESC LIMIT $start, $set[p_str]");

echo '<table class="post">';

if ($k_post == 0)
{
	echo '<div class="mess">';
	echo 'Нет новостей';
	echo '</div>';
}

while ($post = mysql_fetch_assoc($q))
{
	// Лесенка
	echo '<div class="' . ($num % 2 ? "nav1" : "nav2") . '">';
	$num++;
	
	// Заголовок новости
	echo '<a id="link_menu" href="news.php?id=' . $post['id'] . '"><img src="/style/icons/rss.png" alt="*" /> ' . text($post['title']) . '</a> ';
	
	// Колличество комментариев
	echo '(' . mysql_result(mysql_query("SELECT COUNT(*) FROM `news_komm` WHERE `id_news` = '$post[id]'"),0) . ')<br />';
	
	// Часть текста
	echo '<div class="text">' . output_text($post['msg']) . '</div>';
	
	echo '<a href="news.php?id=' . $post['id'] . '">Читать далее &gt;&gt;&gt;</a>';
	echo '</div>';
}

echo '</table>';


// Вывод страниц
if ($k_page>1)str('index.php?',$k_page,$page); 


if (user_access('adm_news'))
{	
	echo '<div class="foot">';
	echo '<img src="/style/icons/ok.gif" alt="*" />  <a href="add.php">Создать новость</a><br />';	
	echo '</div>';
}

include_once '../sys/inc/tfoot.php';
?>

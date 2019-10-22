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

// Если нет id шлем на главную
if (!isset($_GET['id']) && !is_numeric($_GET['id'])){header("Location: index.php?".SID);exit;}

// Cуществование новости
if (mysql_result(mysql_query("SELECT COUNT(*) FROM `news` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1",$db), 0) == 0)
{
	header("Location: index.php?".SID);
	exit;
} 

// Определение записи новости
$news = mysql_fetch_assoc(mysql_query("SELECT * FROM `news` WHERE `id` = '" . intval($_GET['id']) . "' LIMIT 1"));

// Автор новости
$author = get_user($news['id_user']);

// Отмечаем уведомления
if (isset($user))
mysql_query("UPDATE `notification` SET `read` = '1' WHERE `type` = 'news_komm' AND `id_user` = '$user[id]' AND `id_object` = '$news[id]'");


/*------------------------Мне нравится------------------------*/
if (isset($user) && isset($_GET['like']) && ($_GET['like'] == 1 || $_GET['like'] == 0)
    && mysql_result(mysql_query("SELECT COUNT(*) FROM `like_object` WHERE `id_object` = '$news[id]' AND `type` = 'news' AND `id_user` = '$user[id]'"),0) == 0)
{
	mysql_query("INSERT INTO `like_object` (`id_user`, `id_object`, `type`, `like`) VALUES ('$user[id]', '$news[id]', 'news', '" . abs(intval($_GET['like'])) . "')");
	
	// Начисление баллов за активность
	include_once H.'sys/add/user.active.php';
}
/*------------------------------------------------------------*/


// Комментарий 
if (isset($_POST['msg']) && isset($user))
{
	$msg = $_POST['msg'];

	$mat = antimat($msg);
	if ($mat)$err[] = 'В тексте сообщения обнаружен мат: '.$mat;

	if (strlen2($msg)>1024){$err = 'Сообщение слишком длинное';}
	elseif (strlen2($msg)<2){$err = 'Короткое сообщение';}
	elseif (mysql_result(mysql_query("SELECT COUNT(*) FROM `news_komm` WHERE `id_news` = '" . intval($_GET['id']) . "' AND `id_user` = '$user[id]' AND `msg` = '" . my_esc($msg) . "' LIMIT 1"),0) != 0){$err = 'Ваше сообщение повторяет предыдущее';}
	elseif(!isset($err))
	{
		mysql_query("INSERT INTO `news_komm` (`id_user`, `time`, `msg`, `id_news`) values('$user[id]', '$time', '" . my_esc($msg) . "', '" . intval($_GET['id']) . "')");
		
		// Начисление баллов за активность
		include_once H.'sys/add/user.active.php';

		/*
		==========================
		Уведомления об ответах
		==========================
		*/
		
		if (isset($ank_reply['id']))
		{
			$notifiacation = mysql_fetch_assoc(mysql_query("SELECT * FROM `notification_set` WHERE `id_user` = '" . $ank_reply['id'] . "' LIMIT 1"));
			
			if ($notifiacation['komm'] == 1 && $ank_reply['id'] != $user['id'])
			mysql_query("INSERT INTO `notification` (`avtor`, `id_user`, `id_object`, `type`, `time`) VALUES ('$user[id]', '$ank_reply[id]', '$news[id]', 'news_komm', '$time')");
			
		}
		
		$_SESSION['message'] = 'Ваш комментарий успешно принят';
		header('Location: ?id=' . intval($_GET['id']) . '&page=' . intval($_GET['page']));
		exit;
	}
}


$set['title'] = 'Новости - ' . text($news['title']);

include_once '../sys/inc/thead.php';
title();
aut();
err(); 


// Название
echo '<div class="nav1" id="news_title">';
echo '<img src="/style/icons/news.png" alt="*" /> ' . text($news['title']);
echo '</div>';

// Текст новости
echo '<div class="nav2" id="news_content">';
echo output_text($news['msg']);
echo "</div>";

// Мне нравится и автор
echo '<div class="nav2" id="like">';
if (isset($user) && mysql_result(mysql_query("SELECT COUNT(*) FROM `like_object` WHERE `id_object` = '$news[id]' AND `type` = 'news' AND `id_user` = '$user[id]'"),0)==0)
{
	echo '[<img src="/style/icons/like.gif" alt="*"> <a href="?id='.$news['id'].'&amp;like=1">Мне нравится</a>] ';
	echo '[<a href="?id=' . $news['id'] . '&amp;like=0"><img src="/style/icons/dlike.gif" alt="*"></a>]';
}
else
{
	echo '[<img src="/style/icons/like.gif" alt="*"> ' . mysql_result(mysql_query("SELECT COUNT(*) FROM `like_object` WHERE `id_object` = '$news[id]' AND `type` = 'news' AND `like` = '1'"),0) . '] ';
	echo '[<img src="/style/icons/dlike.gif" alt="*"> ' . mysql_result(mysql_query("SELECT COUNT(*) FROM `like_object` WHERE `id_object` = '$news[id]' AND `type` = 'news' AND `like` = '0'"),0) . ']';
}

echo '<br />';

// Автор 
echo 'Опубликовал' . ($author['pol'] == 0 ? 'а' : null) . ': ' 
. group($author['id'])
. user::nick($author['id'])
. medal($author['id']) 
. online($author['id']);
	 
echo '</div>';

// Кнопки соц сетей
echo '<div class="nav2" id="news_share">';
echo 'Поделится:<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
<span class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="vkontakte,twitter,odnoklassniki,moimir"></span>';
echo '</div>';


// Панелька управления
if (user_access('adm_news'))
{
	echo '<div class="nav1" id="news_edit">';
	echo '[<img src="/style/icons/edit.gif" alt="*"> <a href="edit.php?id=' . $news['id'] . '">ред</a>] ';
	echo '[<img src="/style/icons/delete.gif" alt="*"> <a href="delete.php?news_id=' . $news['id'] . '">удл</a>] ';
	echo '</div>';
}

/*----------------------листинг-------------------*/
$listr = mysql_fetch_assoc(mysql_query("SELECT * FROM `news` WHERE `id` < '$news[id]' ORDER BY `id` DESC LIMIT 1"));
$list = mysql_fetch_assoc(mysql_query("SELECT * FROM `news` WHERE `id` > '$news[id]' ORDER BY `id`  ASC LIMIT 1"));

echo '<div class="c2" style="text-align: center;">';
echo '<span class="page">' . ($list['id'] ? '<a href="?id=' . $list['id'].'">&laquo; Пред.</a> ':'&laquo; Пред. ') . '</span>';

$k_1 = mysql_result(mysql_query("SELECT COUNT(*) FROM `news` WHERE `id` > '$news[id]'"),0)+1;
$k_2 = mysql_result(mysql_query("SELECT COUNT(*) FROM `news`"),0);
echo ' (' . $k_1 . ' из ' . $k_2 . ') ';

echo '<span class="page">' . ($listr['id'] ? '<a href="?id=' . $listr['id'] . '">След. &raquo;</a>' : ' След. &raquo;') . '</span>';
echo '</div>';
/*----------------------alex-borisi---------------*/

echo '<div class="foot" id="news_komm">';
echo 'Комментарии:';
echo '</div>';

// Колличество комментариев
$k_post = mysql_result(mysql_query("SELECT COUNT(*) FROM `news_komm` WHERE `id_news` = '".intval($_GET['id'])."' "),0);

$k_page = k_page($k_post,$set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];

// Выборка постов
$q = mysql_query("SELECT * FROM `news_komm` WHERE `id_news` = '" . intval($_GET['id']) . "' ORDER BY `id` $sort LIMIT $start, $set[p_str]");

echo '<table class="post">';

if ($k_post == 0)
{
	echo '<div class="mess" id="no_object">';
	echo 'Нет сообщений';
	echo '</div>';
}
else
{
	/*------------сортировка по времени--------------*/
	if (isset($user))
	{
		echo '<div id="comments" class="menus">';
		echo '<div class="webmenu">';
		echo '<a href="?id=' . $news['id'] . '&amp;page=' . $page . '&amp;sort=1" class="' . ($user['sort'] == 1 ? 'activ' : null) . '">Внизу</a>';
		echo '</div>'; 
		
		echo '<div class="webmenu">';
		echo '<a href="?id=' . $news['id'] . '&amp;page=' . $page . '&amp;sort=0" class="' . ($user['sort'] == 0 ? 'activ' : null) . '">Вверху</a>';
		echo '</div>'; 
		echo '</div>';
	}
	/*---------------alex-borisi---------------------*/
}


while ($post = mysql_fetch_assoc($q))
{
	$ank = mysql_fetch_assoc(mysql_query("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));

	// Лесенка
	echo '<div class="' . ($num % 2 ? "nav1" : "nav2") . '">';
	$num++;

	echo group($ank['id']) . user::nick($ank['id']);

	if (isset($user) && $user['id'] != $ank['id'])
	echo ' <a href="?id=' . $news['id'] . '&amp;page=' . $page . '&amp;response=' . $ank['id'] . '">[*]</a> ';

	echo medal($ank['id']) . online($ank['id']) . ' (' . vremja($post['time']) . ')<br />';

	echo output_text($post['msg']) . '<br />';

	if (isset($user)) 
	{
		echo '<div class="right">';

		if (isset($user) && ($user['level'] > $ank['level'] || $user['level'] != 0 && $user['id'] == $ank['id']))
		echo '<a href="delete.php?id=' . $post['id'] . '"><img src="/style/icons/delete.gif" alt="*"></a>';

		echo '</div>';
	}

	echo '</div>';
}

echo '</table>';

// Вывод страниц
if ($k_page>1)str("news.php?id=" . intval($_GET['id']) . '&amp;', $k_page,$page); 

// Форма для комментариев
if (isset($user))
{
	echo '<form method="post" name="message" action="?id=' . intval($_GET['id']) . '&amp;page=' . $page . REPLY . '">';
	if (is_file(H.'style/themes/' . $set['set_them'] . '/altername_post_form.php'))
	include_once H.'style/themes/' . $set['set_them'] . '/altername_post_form.php';
	else
	echo $tPanel . '<textarea name="msg">' . $insert . '</textarea><br />';
	echo '<input value="Отправить" type="submit" />';
	echo '</form>';
}


echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*"> <a href="index.php">К новостям</a><br />';
echo '</div>';

include_once '../sys/inc/tfoot.php';
?>
<?
include_once '../../../sys/inc/start.php';
include_once '../../../sys/inc/compress.php';
include_once '../../../sys/inc/sess.php';
include_once '../../../sys/inc/home.php';
include_once '../../../sys/inc/settings.php';
include_once '../../../sys/inc/db_connect.php';
include_once '../../../sys/inc/ipua.php';
include_once '../../../sys/inc/fnc.php';
include_once '../../../sys/inc/user.php';


$set['title']='Жалобы'; // заголовок страницы

include_once '../../../sys/inc/thead.php';
title();


err();
aut(); // форма авторизации
if (user_access('adm_panel_show')){
if ($user['group_access']==2)
{
$types = " where `types` = 'chat' ";
}
elseif ($user['group_access']==3)
{
$types =" where `types` = 'forum' ";
}
elseif ($user['group_access']==4)
{
$types = " where (`types` = 'obmen_komm' OR `types` = 'files_komm') ";
}
elseif ($user['group_access']==5)
{
$types = " where `types` = 'lib_komm' ";
}
elseif ($user['group_access']==6)
{
$types = " where `types` = 'foto_komm' ";
}
elseif ($user['group_access']==11)
{
$types = " where `types` = 'notes_komm' ";

}
elseif ($user['group_access']==12)
{
$types = " where `types` = 'guest' ";
}
elseif (($user['group_access']>6 && $user['group_access']<10) || $user['group_access']==15)
{
$types = null;
}


$k_post=mysql_result(mysql_query("SELECT COUNT(*) FROM `spamus` $types"),0);
$k_page=k_page($k_post,$set['p_str']);
$page=page($k_page);
$start=$set['p_str']*$page-$set['p_str'];

echo "<table class='post'>\n";
if ($k_post==0)
{
echo "<div class='mess'>\n";
echo "Нет новых жалоб\n";
echo "</div>\n";
}else{
echo "<div class='mess'>\n";
echo "Внимание! После рассмотрения жалобы не забывайте ее удалить!";
echo "</div>\n";
}

$q=mysql_query("SELECT * FROM `spamus` $types ORDER BY id DESC LIMIT $start, $set[p_str]");

while ($post = mysql_fetch_assoc($q))
{

/*-----------зебра-----------*/
if ($num==0)
{echo "  <div class='nav1'>\n";
$num=1;
}elseif ($num==1)
{echo "  <div class='nav2'>\n";
$num=0;}
/*---------------------------*/


$ank=get_user($post['id_user']);
$spamer = get_user($post['id_spam']);
echo "<b>Раздел:</b> ";

if ($post['razdel'] == 'mail')echo "<font color='red'>Почта</font><br />";

if ($post['razdel'] == 'guest')echo "<a href='/guest/'><font color='red'>Гостевая</font></a><br />";

if ($post['razdel'] == 'files_komm'){  // Файлы юзеров

	$file_id = mysql_fetch_assoc(mysql_query("SELECT * FROM `obmennik_files` WHERE `id` = '$post[id_object]' LIMIT 1"));
	$dir = mysql_fetch_assoc(mysql_query("SELECT * FROM `user_files` WHERE `id` = '$file_id[my_dir]' LIMIT 1"));
	echo "<font color='red'>Личные файлы</font> | ";
	echo " <a href='/user/personalfiles/$file_id[id_user]/$dir[id]/?id_file=$file_id[id]'>".htmlspecialchars($file_id['name'])."</a><br />\n";
	
} 


if ($post['razdel'] == 'obmen_komm'){  // Обменник

	$file_id=mysql_fetch_assoc(mysql_query("SELECT * FROM `obmennik_files` WHERE `id` = '$post[id_object]' LIMIT 1"));
	$dir_id=mysql_fetch_assoc(mysql_query("SELECT * FROM `obmennik_dir` WHERE `id` = '$file_id[id_dir]' LIMIT 1"));
	
	echo "<font color='red'>Зона обмена</font> | ";
	echo " <a href='/obmen$dir_id[dir]$file_id[id].$file_id[ras]?showinfo'>".htmlspecialchars($file_id['name'])."</a><br />\n";
	
} 

if ($post['razdel'] == 'notes_komm'){  // Дневники

	$notes=mysql_fetch_assoc(mysql_query("SELECT * FROM `notes` WHERE `id` = '$post[id_object]' LIMIT 1"));
	echo "<font color='red'>Дневники</font> | ";
	echo " <a href='/plugins/notes/list.php?id=$notes[id]'>".htmlspecialchars($notes['name'])."</a><br />\n";
	
}

if ($post['razdel'] == 'forum'){  // Тема форума

	$them=mysql_fetch_assoc(mysql_query("SELECT * FROM `forum_t` WHERE `id` = '$post[id_object]' LIMIT 1"));
	echo "<font color='red'>Форум</font> | ";
	echo " <a href='/forum/$them[id_forum]/$them[id_razdel]/$them[id]/'>".htmlspecialchars($them['name'])."</a><br />\n";
	
}

if ($post['razdel'] == 'loads_komm'){  // Загрузки

	$komm=mysql_fetch_assoc(mysql_query("SELECT * FROM `loads_komm` WHERE `id` = '$post[id_object]' LIMIT 1"));
	$file=mysql_fetch_assoc(mysql_query("SELECT * FROM `loads_list` WHERE `name` = '$komm[file]' LIMIT 1"));
	echo "<font color='red'>Загрузки</font> | ";
	echo " <a href='/loads/?komm&d=$file[path]&f=$file[name]'>".htmlspecialchars($file['name'])."</a><br />\n";
	
}

if ($post['razdel'] == 'foto_komm'){  // Фотографии

	$foto=mysql_fetch_assoc(mysql_query("SELECT * FROM `gallery_foto` WHERE `id` = '$post[id_object]' LIMIT 1"));
	echo "<font color='red'>Фото</font> | ";
	echo " <a href='/foto/$foto[id_user]/$foto[id_gallery]/$foto[id]/'>".htmlspecialchars($foto['name'])."</a><br />\n";
	
}

if ($post['razdel'] == 'stena') // Стена юзера
{
	echo "<font color='red'>Стена</font> | ";
	$anketa = get_user($post['id_object']);
	echo " <a href='/info.php?id=$anketa[id]'>$anketa[nick]</a>\n";
	echo " ".medal($anketa['id'])." ".online($anketa['id'])."<br />";
}

if ($post['razdel'] == 'status_komm')	// Статус
{ 
	$status=mysql_fetch_assoc(mysql_query("SELECT * FROM `status` WHERE `id` = '$post[id_object]' LIMIT 1"));
	echo "<a href='/user/status/komm.php?id=$status[id]'><font color='red'>Статус</font></a> | "; 
	$anketa = get_user($status['id_user']);
	echo " <a href='/info.php?id=$anketa[id]'>$anketa[nick]</a>\n";
	echo " ".medal($anketa['id'])." ".online($anketa['id'])."<br />";
}


echo "<b>Жалоба от:</b> <a href='/info.php?id=$ank[id]'>$ank[nick]</a>\n";
echo " ".medal($ank['id'])." ".online($ank['id'])." (".vremja($post['time']).")<br />";

if ($post['razdel']=='mail' || $post['razdel']=='guest' || $post['razdel']=='forum' || $post['razdel']=='stena')
echo "<b>На сообщение:</b> <font color='red' style='border-bottom: 1px solid green;'>".output_text($post['spam'])."<br /></font>\n";
echo "<b>Комментарий:</b> ".output_text($post['msg'])."<br />";

echo "<b>Нарушитель:</b>  <a href='/info.php?id=$spamer[id]'>$spamer[nick]</a>";
echo "".medal($spamer['id'])." ".online($spamer['id'])."<br />";

echo "   </div>\n";
if (($user['id']!=$spamer['id'] && $user['group_access']>=$spamer['group_access']) || ($user['id']==1))
{
echo "<div class='mess'>[<a href='/adm_panel/ban.php?id=$spamer[id]'><img src='/style/icons/blicon.gif' alt='*'> бан</a>] 
[<a href='delete.php?id=$post[id]&amp;otkl'><img src='/style/icons/delete.gif' alt='*'> отклонить</a>] [<a href='delete.php?id=$post[id]'><img src='/style/icons/ok.gif' alt='*'> рассмотрена</a>] </div>\n";
}else if ($user['id']==$spamer['id']){echo "<div class='mess'>На вас поступила жалоба от <font color='green'>$ank[nick]</font> 
пожалуста дождитесь администратора для выяснения обстоятельств.</div>\n";}
else{echo "<div class='mess'>У вас не достаточно полномочий, для рассмотрения этой жалобы.</div>\n";}
echo "</table>\n";
}



if ($k_page>1)str('?',$k_page,$page); // Вывод страниц


echo "<div class='foot'>\n";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/plugins/admin/'>Админ раздел</a><br />\n";
echo "</div>\n";
}
include_once '../../../sys/inc/tfoot.php';
?>

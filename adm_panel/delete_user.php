<?
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/adm_check.php';
include_once '../sys/inc/user.php';
user_access('user_delete',null,'index.php?'.SID);
adm_check();


if (isset($_GET['id']))$ank['id']=intval($_GET['id']);else {header("Location: /index.php?".SID);exit;}


if (mysql_result(mysql_query("SELECT COUNT(*) FROM `user` WHERE `id` = '$ank[id]' LIMIT 1"),0)==0){header("Location: /index.php?".SID);exit;}
$ank=get_user($ank['id']);
if ($user['level']<=$ank['level']){header("Location: /index.php?".SID);exit;}


$set['title']='Удаление пользователя "'.$ank['nick'].'"';
include_once '../sys/inc/thead.php';
title();


if (isset($_POST['delete']))
{
if (function_exists('set_time_limit'))@set_time_limit(600);
$mass[0]=$ank['id'];
$collisions=user_collision($mass,1);
mysql_query("DELETE FROM `user` WHERE `id` = '$ank[id]' LIMIT 1");
mysql_query("DELETE FROM `chat_post` WHERE `id_user` = '$ank[id]'");
mysql_query("DELETE FROM `gifts_user` WHERE `id_user` = '$ank[id]' OR `id_ank` = '$ank[id]'");
mysql_query("DELETE FROM `frends` WHERE `user` = '$ank[id]' OR `frend` = '$ank[id]'");	
mysql_query("DELETE FROM `frends_new` WHERE `user` = '$ank[id]' OR `to` = '$ank[id]'");	
	
	mysql_query("DELETE FROM `stena` WHERE `id_user` = '$ank[id]'");
	mysql_query("DELETE FROM `stena_like` WHERE `id_user` = '$ank[id]'");	
	mysql_query("DELETE FROM `status_like` WHERE `id_user` = '$ank[id]'");	
	mysql_query("DELETE FROM `status` WHERE `id_user` = '$ank[id]'");	
	mysql_query("DELETE FROM `status_komm` WHERE `id_user` = '$ank[id]'");	

$q5=mysql_query("SELECT * FROM `forum_t` WHERE `id_user` = '$ank[id]'");
while ($post5 = mysql_fetch_assoc($q5))
{
mysql_query("DELETE FROM `forum_p` WHERE `id_them` = '$post5[id]'");
}
mysql_query("DELETE FROM `forum_t` WHERE `id_user` = '$ank[id]'");


mysql_query("DELETE FROM `user_set` WHERE `id_user` = '$ank[id]'");
mysql_query("DELETE FROM `notification` WHERE `id_user` = '$ank[id]'");
mysql_query("DELETE FROM `notification_set` WHERE `id_user` = '$ank[id]'");
mysql_query("DELETE FROM `discussions` WHERE `id_user` = '$ank[id]' OR `id_user` = '$ank[id]' OR `ot_kogo` = '$ank[id]'");
mysql_query("DELETE FROM `discussions_set` WHERE `id_user` = '$ank[id]'");

mysql_query("DELETE FROM `forum_p` WHERE `id_user` = '$ank[id]'");
mysql_query("DELETE FROM `forum_zakl` WHERE `id_user` = '$ank[id]'");
mysql_query("DELETE FROM `guest` WHERE `id_user` = '$ank[id]'");
mysql_query("DELETE FROM `loads_komm` WHERE `id_user` = '$ank[id]'");
mysql_query("DELETE FROM `news_komm` WHERE `id_user` = '$ank[id]'");
mysql_query("DELETE FROM `user_files` WHERE `id_user` = '$ank[id]'");
mysql_query("DELETE FROM `user_music` WHERE `id_user` = '$ank[id]'");
mysql_query("DELETE FROM `like_object` WHERE `id_user` = '$ank[id]'");

mysql_query("DELETE FROM `status` WHERE `id_user` = '$ank[id]'");
mysql_query("DELETE FROM `status_like` WHERE `id_user` = '$ank[id]'");
mysql_query("DELETE FROM `status_komm` WHERE `id_user` = '$ank[id]'");
mysql_query("DELETE FROM `status_count` WHERE `id_user` = '$ank[id]'");

mysql_query("DELETE FROM `mark_notes` WHERE `id_user` = '$ank[id]'");
mysql_query("DELETE FROM `mark_files` WHERE `id_user` = '$ank[id]'");
mysql_query("DELETE FROM `mark_people` WHERE `id_user` = '$ank[id]'");
mysql_query("DELETE FROM `mark_foto` WHERE `id_user` = '$ank[id]'");

mysql_query("DELETE FROM `tape_set` WHERE `id_user` = '$ank[id]'");
mysql_query("DELETE FROM `tape` WHERE `id_user` = '$ank[id]'");
mysql_query("DELETE FROM `tape` WHERE `avtor` = '$ank[id]'");
mysql_query("DELETE FROM `tape` WHERE `id_file` = '$ank[id]' AND `type` = 'frend'");


$opdirbase=@opendir(H.'sys/add/delete_user_act');
while ($filebase=@readdir($opdirbase))
if (preg_match('#\.php$#i',$filebase))
include_once(H.'sys/add/delete_user_act/'.$filebase);

$q5=mysql_query("SELECT * FROM `obmennik_files` WHERE `id_user` = '$ank[id]'");
while ($post5 = mysql_fetch_assoc($q5))
{
unlink(H.'sys/obmen/files/'.$post5['id'].'.dat');
}


mysql_query("DELETE FROM `obmennik_files` WHERE `id_user` = '$ank[id]'");

mysql_query("DELETE FROM `users_konts` WHERE `id_user` = '$ank[id]' OR `id_kont` = '$ank[id]'");
mysql_query("DELETE FROM `mail` WHERE `id_user` = '$ank[id]' OR `id_kont` = '$ank[id]'");
mysql_query("DELETE FROM `user_voice` WHERE `id_user` = '$ank[id]' OR `id_kont` = '$ank[id]'");
mysql_query("DELETE FROM `user_collision` WHERE `id_user` = '$ank[id]' OR `id_user2` = '$ank[id]'");
mysql_query("DELETE FROM `votes_user` WHERE `u_id` = '$ank[id]'");


if (count($collisions)>1 && isset($_GET['all']))
{
for ($i=1;$i<count($collisions);$i++)
{
mysql_query("DELETE FROM `user` WHERE `id` = '$collisions[$i]' LIMIT 1");
mysql_query("DELETE FROM `chat_post` WHERE `id_user` = '$collisions[$i]'");


mysql_query("DELETE FROM `forum_t` WHERE `id_user` = '$collisions[$i]'");


$q5=mysql_query("SELECT * FROM `forum_t` WHERE `id_user` = '$collisions[$i]'");
while ($post5 = mysql_fetch_assoc($q5))
{
mysql_query("DELETE FROM `forum_p` WHERE `id_them` = '$post5[id]'");
}

mysql_query("DELETE FROM `forum_p` WHERE `id_user` = '$collisions[$i]'");
mysql_query("DELETE FROM `forum_zakl` WHERE `id_user` = '$collisions[$i]'");
mysql_query("DELETE FROM `guest` WHERE `id_user` = '$collisions[$i]'");
mysql_query("DELETE FROM `loads_komm` WHERE `id_user` = '$collisions[$i]'");
mysql_query("DELETE FROM `news_komm` WHERE `id_user` = '$collisions[$i]'");


$q5=mysql_query("SELECT * FROM `obmennik_files` WHERE `id_user` = '$collisions[$i]'");
while ($post5 = mysql_fetch_assoc($q5))
{
unlink(H.'sys/obmen/files/'.$post5['id'].'.dat');
}
mysql_query("DELETE FROM `obmennik_files` WHERE `id_user` = '$collisions[$i]'");

mysql_query("DELETE FROM `users_konts` WHERE `id_user` = '$collisions[$i]' OR `id_kont` = '$collisions[$i]'");
mysql_query("DELETE FROM `mail` WHERE `id_user` = '$collisions[$i]' OR `id_kont` = '$collisions[$i]'");
mysql_query("DELETE FROM `user_voice` WHERE `id_user` = '$collisions[$i]' OR `id_kont` = '$collisions[$i]'");
mysql_query("DELETE FROM `user_collision` WHERE `id_user` = '$collisions[$i]' OR `id_user2` = '$collisions[$i]'");
mysql_query("DELETE FROM `votes_user` WHERE `u_id` = '$collisions[$i]'");
}
admin_log('Пользователи','Удаление',"Удаление группы пользователей '$ank[nick]' (id#".implode(',id#',$collisions).")");
msg('Все данные о пользователях удалены');
}
else 
{
admin_log('Пользователи','Удаление',"Удаление пользователя '$ank[nick]' (id#$ank[id])");
msg("Все данные о пользователе $ank[nick] удалены");
}



$tab=mysql_list_tables($set['mysql_db_name']);
for($i=0;$i<mysql_num_rows($tab);$i++)
{
mysql_query("OPTIMIZE TABLE `".mysql_tablename($tab,$i)."`");
}

echo "<div class='foot'>\n";
echo "&laquo;<a href='/users.php'>Пользователи</a><br />\n";
echo "</div>\n";
include_once '../sys/inc/tfoot.php';
}






$mass[0]=$ank['id'];
$collisions=user_collision($mass,1);


$chat_post=mysql_result(mysql_query("SELECT COUNT(*) FROM `chat_post` WHERE `id_user` = '$ank[id]'"),0);
if (count($collisions)>1 && isset($_GET['all']))
{
$chat_post_coll=0;
for ($i=1;$i<count($collisions);$i++)
{
$chat_post_coll+=mysql_result(mysql_query("SELECT COUNT(*) FROM `chat_post` WHERE `id_user` = '$collisions[$i]'"),0);
}

if ($chat_post_coll!=0)
$chat_post="$chat_post +$chat_post_coll*";
}
echo "<span class=\"ank_n\">Сообщений в чате:</span> <span class=\"ank_d\">$chat_post</span><br />\n";


$k_them=mysql_result(mysql_query("SELECT COUNT(*) FROM `forum_t` WHERE `id_user` = '$ank[id]'"),0);
if (count($collisions)>1 && isset($_GET['all']))
{
$k_them_coll=0;
for ($i=1;$i<count($collisions);$i++)
{
$k_them_coll+=mysql_result(mysql_query("SELECT COUNT(*) FROM `forum_t` WHERE `id_user` = '$collisions[$i]'"),0);
}
if ($k_them_coll!=0)
$k_them="$k_them +$k_them_coll*";
}
echo "<span class=\"ank_n\">Тем в форуме:</span> <span class=\"ank_d\">$k_them</span><br />\n";

$k_p_forum=mysql_result(mysql_query("SELECT COUNT(*) FROM `forum_p` WHERE `id_user` = '$ank[id]'"),0);
if (count($collisions)>1 && isset($_GET['all']))
{
$k_p_forum_coll=0;
for ($i=1;$i<count($collisions);$i++)
{
$k_p_forum_coll+=mysql_result(mysql_query("SELECT COUNT(*) FROM `forum_p` WHERE `id_user` = '$collisions[$i]'"),0);
}
if ($k_p_forum_coll!=0)
$k_p_forum="$k_p_forum +$k_p_forum_coll*";
}
echo "<span class=\"ank_n\">Соощений в форуме:</span> <span class=\"ank_d\">$k_p_forum</span><br />\n";



$zakl=mysql_result(mysql_query("SELECT COUNT(*) FROM `forum_zakl` WHERE `id_user` = '$ank[id]'"),0);
if (count($collisions)>1 && isset($_GET['all']))
{
$zakl_coll=0;
for ($i=1;$i<count($collisions);$i++)
{
$zakl_coll+=mysql_result(mysql_query("SELECT COUNT(*) FROM `forum_zakl` WHERE `id_user` = '$collisions[$i]'"),0);
}
if ($zakl_coll!=0)
$zakl="$zakl +$zakl_coll*";
}
echo "<span class=\"ank_n\">Закладок:</span> <span class=\"ank_d\">$zakl</span><br />\n";







$guest=mysql_result(mysql_query("SELECT COUNT(*) FROM `guest` WHERE `id_user` = '$ank[id]'"),0);
if (count($collisions)>1 && isset($_GET['all']))
{
$guest_coll=0;
for ($i=1;$i<count($collisions);$i++)
{
$guest_coll+=mysql_result(mysql_query("SELECT COUNT(*) FROM `guest` WHERE `id_user` = '$collisions[$i]'"),0);
}
if ($guest_coll!=0)
$guest="$guest +$guest_coll*";
}
echo "<span class=\"ank_n\">Гостевая:</span> <span class=\"ank_d\">$guest</span><br />\n";




$konts=mysql_result(mysql_query("SELECT COUNT(*) FROM `users_konts` WHERE `id_user` = '$ank[id]' OR `id_kont` = '$ank[id]'"),0);
if (count($collisions)>1 && isset($_GET['all']))
{
$konts_coll=0;
for ($i=1;$i<count($collisions);$i++)
{
$konts_coll+=mysql_result(mysql_query("SELECT COUNT(*) FROM `users_konts` WHERE `id_user` = '$collisions[$i]' OR `id_kont` = '$collisions[$i]'"),0);
}
if ($konts_coll!=0)
$konts="$konts +$konts_coll*";
}
echo "<span class=\"ank_n\">Контакты:</span> <span class=\"ank_d\">$konts</span><br />\n";



$mail=mysql_result(mysql_query("SELECT COUNT(*) FROM `mail` WHERE `id_user` = '$ank[id]' OR `id_kont` = '$ank[id]'"),0);
if (count($collisions)>1 && isset($_GET['all']))
{
$mail_coll=0;
for ($i=1;$i<count($collisions);$i++)
{
$mail_coll+=mysql_result(mysql_query("SELECT COUNT(*) FROM `mail` WHERE `id_user` = '$collisions[$i]' OR `id_kont` = '$collisions[$i]'"),0);
}
if ($mail_coll!=0)
$mail="$mail +$mail_coll*";
}
echo "<span class=\"ank_n\">Приватные сообщения:</span> <span class=\"ank_d\">$mail</span><br />\n";



$komm_loads=mysql_result(mysql_query("SELECT COUNT(*) FROM `loads_komm` WHERE `id_user` = '$ank[id]'"),0);
if (count($collisions)>1 && isset($_GET['all']))
{
$komm_loads_coll=0;
for ($i=1;$i<count($collisions);$i++)
{
$komm_loads_coll+=mysql_result(mysql_query("SELECT COUNT(*) FROM `loads_komm` WHERE `id_user` = '$collisions[$i]'"),0);
}
if ($komm_loads_coll!=0)
$komm_loads="$komm_loads +$komm_loads_coll*";
}
echo "<span class=\"ank_n\">Комментарии в загрузках:</span> <span class=\"ank_d\">$komm_loads</span><br />\n";





$news_komm=mysql_result(mysql_query("SELECT COUNT(*) FROM `news_komm` WHERE `id_user` = '$ank[id]'"),0);
if (count($collisions)>1 && isset($_GET['all']))
{
$news_komm_coll=0;
for ($i=1;$i<count($collisions);$i++)
{
$news_komm_coll+=mysql_result(mysql_query("SELECT COUNT(*) FROM `news_komm` WHERE `id_user` = '$collisions[$i]'"),0);
}
if ($news_komm_coll!=0)
$news_komm="$news_komm +$news_komm_coll*";
}
echo "<span class=\"ank_n\">Комментарии новостей:</span> <span class=\"ank_d\">$news_komm</span><br />\n";





$user_voice=mysql_result(mysql_query("SELECT COUNT(*) FROM `user_voice2` WHERE `id_user` = '$ank[id]' OR `id_kont` = '$ank[id]'"),0);
if (count($collisions)>1 && isset($_GET['all']))
{
$user_voice_coll=0;
for ($i=1;$i<count($collisions);$i++)
{
$user_voice_coll+=mysql_result(mysql_query("SELECT COUNT(*) FROM `user_voice2` WHERE `id_user` = '$collisions[$i]' OR `id_kont` = '$collisions[$i]'"),0);
}
if ($user_voice_coll!=0)
$user_voice="$user_voice +$user_voice_coll*";
}
echo "<span class=\"ank_n\">Рейтинги:</span> <span class=\"ank_d\">$user_voice</span><br />\n";







$obmennik=mysql_result(mysql_query("SELECT COUNT(*) FROM `obmennik_files` WHERE `id_user` = '$ank[id]'"),0);
if (count($collisions)>1 && isset($_GET['all']))
{
$obmennik_coll=0;
for ($i=1;$i<count($collisions);$i++)
{
$obmennik_coll+=mysql_result(mysql_query("SELECT COUNT(*) FROM `obmennik_files` WHERE `id_user` = '$collisions[$i]'"),0);
}
if ($obmennik_coll!=0)
$obmennik="$obmennik +$obmennik_coll*";
}
echo "<span class=\"ank_n\">Файлы в обменнике:</span> <span class=\"ank_d\">$obmennik</span><br />\n";



$opdirbase=@opendir(H.'sys/add/delete_user_info');
while ($filebase=@readdir($opdirbase))
if (preg_match('#\.php$#i',$filebase))
include_once(H.'sys/add/delete_user_info/'.$filebase);



echo "<form method=\"post\" action=\"\">\n";
echo "<input value=\"Удалить\" type=\"submit\" name='delete' />\n";
echo "</form>\n";


if (count($collisions)>1 && isset($_GET['all']))
{
echo "* Также будут удалены пользователи:\n";


for ($i=1;$i<count($collisions);$i++)
{
$ank_coll=mysql_fetch_assoc(mysql_query("SELECT * FROM `user` WHERE `id` = '$collisions[$i]'"));
echo "$ank_coll[nick]";
if ($i==count($collisions)-1)
echo '.'; else echo '; ';
}
echo "<br />\n";
}



echo "Удаленные данные невозможно будет восстановить<br />\n";


echo "<div class='foot'>\n";
echo "&laquo;<a href='/info.php?id=$ank[id]'>В анкету</a><br />\n";
echo "&laquo;<a href='/users.php'>Пользователи</a><br />\n";

echo "</div>\n";
include_once '../sys/inc/tfoot.php';
?>
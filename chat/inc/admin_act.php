<?

if (user_access('chat_room') && isset($_GET['set']) && isset($_GET['ok']) && is_numeric($_GET['set']) && mysql_result(mysql_query("SELECT COUNT(*) FROM `chat_rooms` WHERE `id` = '".intval($_GET['set'])."'"),0)==1)
{
$room=mysql_fetch_assoc(mysql_query("SELECT * FROM `chat_rooms` WHERE `id` = '".intval($_GET['set'])."' LIMIT 1"));
$name=esc(stripcslashes(htmlspecialchars($_POST['name'])));
$opis=my_esc($_POST['opis']);
$pos=intval($_POST['pos']);

if ($_POST['bots']==1 || $_POST['bots']==3)$umnik=1;else $umnik=0;
if ($_POST['bots']==2 || $_POST['bots']==3)$shutnik=1;else $shutnik=0;
mysql_query("UPDATE `chat_rooms` SET `name` = '$name', `opis` = '$opis', `pos` = '$pos', `umnik` = '$umnik', `shutnik` = '$shutnik' WHERE `id` = '$room[id]' LIMIT 1");
admin_log('Чат','Параметры комнат',"Изменение комнаты $name");
msg('Параметры комнаты изменены');
}


if (user_access('chat_room') && isset($_GET['act']) && isset($_GET['ok']) && $_GET['act']=='add_room' && isset($_POST['name']) && esc($_POST['name'])!=NULL)
{

$name=esc(stripcslashes(htmlspecialchars($_POST['name'])));
$opis=my_esc($_POST['opis']);
$pos=intval($_POST['pos']);

if ($_POST['bots']==1 || $_POST['bots']==3)$umnik=1;else $umnik=0;
if ($_POST['bots']==2 || $_POST['bots']==3)$shutnik=1;else $shutnik=0;
mysql_query("INSERT INTO `chat_rooms` (`name`, `opis`, `pos`, `umnik`, `shutnik`) values('$name', '$opis', '$pos', '$umnik', '$shutnik')");
admin_log('Чат','Параметры комнат',"Добавлена комната '$name', описание: $opis");
msg('Комната успешно добавлена');
}

if (user_access('chat_room') && isset($_GET['delete']) && is_numeric($_GET['delete']) && mysql_result(mysql_query("SELECT COUNT(*) FROM `chat_rooms` WHERE `id` = '".intval($_GET['delete'])."'"),0)==1)
{
$room=mysql_fetch_assoc(mysql_query("SELECT * FROM `chat_rooms` WHERE `id` = '".intval($_GET['delete'])."' LIMIT 1"));
mysql_query("DELETE FROM `chat_rooms` WHERE `id` = '$room[id]' LIMIT 1");
mysql_query("DELETE FROM `chat_post` WHERE `room` = '$room[id]'");
admin_log('Чат','Параметры комнат',"Удалена комната '$room[name]'");
msg('Комната успешно удалена');
}

if (user_access('chat_clear') && isset($_GET['act']) && $_GET['act']=='clear2')
{
admin_log('Чат','Очистка',"Очистка комнат от сообщений");
mysql_query("TRUNCATE `chat_post`");
msg('Все комнаты очищены');
}
?>
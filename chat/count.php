<?
if (isset($user))mysql_query("DELETE FROM `chat_who` WHERE `id_user` = '$user[id]'");
mysql_query("DELETE FROM `chat_who` WHERE `time` < '".($time-120)."'");
echo '('.mysql_result(mysql_query("SELECT COUNT(*) FROM `chat_who`"),0).' человек)';
?>
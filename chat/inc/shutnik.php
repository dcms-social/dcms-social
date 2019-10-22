<?
$shutnik_last = mysql_fetch_assoc(mysql_query("SELECT * FROM `chat_post` WHERE `room` = '$room[id]' AND `shutnik` = '1' ORDER BY id DESC LIMIT 1"));
if ($shutnik_last==NULL || $shutnik_last['time']<time()-$set['shutnik_new'])
{
$k_vopr=mysql_result(mysql_query("SELECT COUNT(*) FROM `chat_shutnik`"),0);
$shutnik = mysql_fetch_assoc(mysql_query("SELECT * FROM `chat_shutnik` LIMIT ".rand(0,$k_vopr).",1"));
mysql_query("INSERT INTO `chat_post` (`shutnik`, `time`, `msg`, `room`, `privat`) values('1', '$time', '$shutnik[anek]', '$room[id]', '0')");
}
?>
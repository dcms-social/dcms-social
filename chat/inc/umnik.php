<?
$umnik_last = mysql_fetch_assoc(mysql_query("SELECT * FROM `chat_post` WHERE `room` = '$room[id]' AND `umnik_st` <> '0' ORDER BY id DESC"));
if ($umnik_last!=NULL && $umnik_last['umnik_st']!=4 && $umnik_last['umnik_st']!=0)
{
$umnik_vopros = mysql_fetch_assoc(mysql_query("SELECT * FROM `chat_vopros` WHERE `id` = '$umnik_last[vopros]' LIMIT 1"));
$umnik_post = mysql_fetch_assoc(mysql_query("SELECT * FROM `chat_post` WHERE `room` = '$room[id]' AND `msg` like '%$umnik_vopros[otvet]%' AND `umnik_st` = '0' AND `time` >= '".($time-$umnik_last['time'])."' ORDER BY `id` ASC LIMIT 1"));
if($umnik_post!=NULL){

$ank=mysql_fetch_assoc(mysql_query("SELECT * FROM `user` WHERE `id` = '$umnik_post[id_user]' LIMIT 1"));


$add_balls=0;

if ($umnik_last['umnik_st']==1){$add_balls=25;$pods='не используя подсказок';}
if ($umnik_last['umnik_st']==2){$add_balls=10;$pods='используя одну подсказку';}
if ($umnik_last['umnik_st']==3){$add_balls=5;$pods='используя обе посказки';}
$msg="Молодец, [b]$ank[nick][/b].\nТы первым дал верный ответ: [b]$umnik_vopros[otvet][/b] $pods.\n[b]$ank[nick][/b] получает $add_balls баллов.\nСледующий вопрос через $set[umnik_new] сек.";
mysql_query("INSERT INTO `chat_post` (`umnik_st`, `time`, `msg`, `room`, `vopros`, `privat`) values('4', '$time', '$msg', '$room[id]', '$umnik_vopros[id]', '0')");

mysql_query("UPDATE `user` SET `balls` = '".($ank['balls']+$add_balls)."' WHERE `id` = '$ank[id]' LIMIT 1");

}
}
$umnik_last1 = mysql_fetch_assoc(mysql_query("SELECT * FROM `chat_post` WHERE `room` = '$room[id]' AND `umnik_st` = '1' ORDER BY id DESC"));
if ($umnik_last1!=NULL && $umnik_last['umnik_st']!=4 && $umnik_last1['time']<time()-$set['umnik_time'])
{
$umnik_vopros = mysql_fetch_assoc(mysql_query("SELECT * FROM `chat_vopros` WHERE `id` = '$umnik_last1[vopros]' LIMIT 1"));
$msg="На вопрос никто не ответил.\nПравильный ответ: $umnik_vopros[otvet].\nСледующий вопрос через $set[umnik_new] сек.";
mysql_query("INSERT INTO `chat_post` (`umnik_st`, `time`, `msg`, `room`, `vopros`, `privat`) values('4', '$time', '$msg', '$room[id]', '$umnik_vopros[id]', '0')");
}
$umnik_last = mysql_fetch_assoc(mysql_query("SELECT * FROM `chat_post` WHERE `room` = '$room[id]' AND `umnik_st` <> '0' ORDER BY id DESC"));
if ($umnik_last==NULL || $umnik_last['umnik_st']==4 && $umnik_last['time']<time()-$set['umnik_new'])
{
// задается вопрос
$k_vopr=mysql_result(mysql_query("SELECT COUNT(*) FROM `chat_vopros`"),0);
$umnik_vopros = mysql_fetch_assoc(mysql_query("SELECT * FROM `chat_vopros` LIMIT ".rand(0,$k_vopr).", 1"));
$msg="[b]Вопрос:[/b] \"$umnik_vopros[vopros]\"\n[b]Ответ:[/b] слово из ".strlen2($umnik_vopros['otvet'])." букв";
mysql_query("INSERT INTO `chat_post` (`umnik_st`, `time`, `msg`, `room`, `vopros`, `privat`) values('1', '$time', '$msg', '$room[id]', '$umnik_vopros[id]', '0')");
}
if ($umnik_last!=NULL && $umnik_last['umnik_st']==1 && $umnik_last['time']<time()-$set['umnik_help'])
{
$umnik_vopros = mysql_fetch_assoc(mysql_query("SELECT * FROM `chat_vopros` WHERE `id` = '$umnik_last[vopros]' LIMIT 1"));
if (function_exists('iconv_substr'))
$help=iconv_substr($umnik_vopros['otvet'], 0, 1, 'utf-8');
else
$help=substr($umnik_vopros['otvet'], 0, 2);
for ($i=0;$i<strlen2($umnik_vopros['otvet'])-1 ;$i++ ) {
	$help.='*';
}
$msg="[b]Вопрос:[/b] \"$umnik_vopros[vopros]\"\n[b]Первая подсказка:[/b] $help (".strlen2($umnik_vopros['otvet'])." букв)";
mysql_query("INSERT INTO `chat_post` (`umnik_st`, `time`, `msg`, `room`, `vopros`, `privat`) values('2', '$time', '$msg', '$room[id]', '$umnik_vopros[id]', '0')");
}
if ($umnik_last!=NULL && $umnik_last['umnik_st']==2 && $umnik_last['time']<time()-$set['umnik_help'])
{
$umnik_vopros = mysql_fetch_assoc(mysql_query("SELECT * FROM `chat_vopros` WHERE `id` = '$umnik_last[vopros]' LIMIT 1"));
if (function_exists('iconv_substr'))
$help=iconv_substr($umnik_vopros['otvet'], 0, 2, 'utf-8');
else
$help=substr($umnik_vopros['otvet'], 0, 4);
for ($i=0;$i<strlen2($umnik_vopros['otvet'])-2 ;$i++ ) {
	$help.='*';
}
$msg="[b]Вопрос:[/b] \"$umnik_vopros[vopros]\"\n[b]Вторая подсказка:[/b] $help (".strlen2($umnik_vopros['otvet'])." букв)";
mysql_query("INSERT INTO `chat_post` (`umnik_st`, `time`, `msg`, `room`, `vopros`, `privat`) values('3', '$time', '$msg', '$room[id]', '$umnik_vopros[id]', '0')");
}
?>
<?
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/adm_check.php';
include_once '../../sys/inc/user.php';

$set['title']='Перевод баллов';
include_once '../../sys/inc/thead.php';
title();
if (!isset($user))header("location: /index.php?");

err();
aut();
if (isset($user) && isset($_POST['title']) && $_POST['title'] > 0)
{
if ($_POST['title']==1)
{
$money="500";
$m="".intval($_POST['title'])."";
}
elseif ($_POST['title']==2)
{
$money="1000";
$m="".intval($_POST['title'])."";
}
elseif ($_POST['title']==3)
{
$money="1500";
$m="".intval($_POST['title'])."";
}
elseif ($_POST['title']==4)
{
$money="2000";
$m="".intval($_POST['title'])."";
}
elseif ($_POST['title']==5)
{
$money="2500";
$m="".intval($_POST['title'])."";
}
elseif ($_POST['title']==6)
{
$money="3000";
$m="".intval($_POST['title'])."";
}
elseif ($_POST['title']==7)
{
$money="3500";
$m="".intval($_POST['title'])."";
}
elseif ($_POST['title']==8)
{
$money="4000";
$m="".intval($_POST['title'])."";
}
elseif ($_POST['title']==9)
{
$money="4500";
$m="".intval($_POST['title'])."";
}
elseif ($_POST['title']==10)
{
$money="5000";
$m="".intval($_POST['title'])."";
}
elseif ($_POST['title']==11)
{
$money="5500";
$m="".intval($_POST['title'])."";
}
elseif ($_POST['title']==12)
{
$money="6000";
$m="".intval($_POST['title'])."";
}
elseif ($_POST['title']==13)
{
$money="6500";
$m="".intval($_POST['title'])."";
}
elseif ($_POST['title']==14)
{
$money="7000";
$m="".intval($_POST['title'])."";
}
elseif ($_POST['title']==15)
{
$money="7500";
$m="".intval($_POST['title'])."";
}
elseif ($_POST['title']==16)
{
$money="8000";
$m="".intval($_POST['title'])."";
}
elseif ($_POST['title']==17)
{
$money="8500";
$m="".intval($_POST['title'])."";
}
elseif ($_POST['title']==18)
{
$money="9000";
$m="".intval($_POST['title'])."";
}
elseif ($_POST['title']==19)
{
$money="9500";
$m="".intval($_POST['title'])."";
}
elseif ($_POST['title']==20)
{
$money="10000";
$m="".intval($_POST['title'])."";
}
elseif ($_POST['title']>20)
{
$err='Перевод баллов возможен не более чем 20 '.$sm.' за одну операцию';
}


if ($user['balls'] >= $money)
{
if (!$err){
mysql_query("UPDATE `user` SET `balls` = '" . ($user['balls']-$money) . "' WHERE `id` = '$user[id]' LIMIT 1");
mysql_query("UPDATE `user` SET `money` = '" . ($user['money']+$m) . "' WHERE `id` = '$user[id]' LIMIT 1");
$_SESSION['message'] = 'Поздравляем, пополнение счета успешно произведен';
header("Location: ?");
exit;
}
}
else
{
err('Недостаточно баллов для завершения операции');
}
}

echo "<div class='foot'>\n";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/info.php'>$user[nick]</a> | Обмен $sMonet[0]<br />\n";
echo "</div>\n";

echo "<div class='mess'>\n";
echo "У вас <b>$user[balls]</b> баллов активности.";
echo "</div>\n";

echo "<div class='mess'>\n";
echo "C помощью этого сервиса, ты сможешь перевести заработанные баллы активности в $sMonet[2]<br />
<b>Курс на ".date("m.d.y")." по Москве: 1 $sMonet[1] &rArr; 500 баллов активности.</b>";
echo "</div>\n";

if (isset($user) && $user['balls']>=500)
{
echo "<form class='main' method=\"post\" action=\"money.php\">\n";

echo "Сумма:<br />\n<select name='title'>\n";

echo "<option value='1' selected='selected'><b>1 $sMonet[1]</b></option>\n";
echo "<option value='2' ><b>2 $sMonet[2]</b></option>\n";
echo "<option value='3' ><b>3 $sMonet[2]</b></option>\n";
echo "<option value='4' ><b>4 $sMonet[2]</b></option>\n";
echo "<option value='5' ><b>5 $sMonet[0]</b></option>\n";
echo "<option value='6' ><b>6 $sMonet[0]</b></option>\n";
echo "<option value='7' ><b>7 $sMonet[0]</b></option>\n";

echo "</select><br />\n";

echo "<input value=\"Получить\" type=\"submit\" />\n";
echo "</form>\n";
}
else
{
echo "<div class='err'>\n";
echo "Не достаточно баллов для совершения операции";
echo "</div>\n";
}


echo "<div class='foot'>\n";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/info.php'>$user[nick]</a> | Обмен $sMonet[0]<br />\n";
echo "</div>\n";

include_once '../../sys/inc/tfoot.php';
?>
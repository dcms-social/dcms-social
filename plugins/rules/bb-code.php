<?
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/user.php';
$set['title']='Фон';
include_once '../../sys/inc/thead.php';
title();
err();
aut();
echo "<div class='foot'>\n";
echo 'Стили текста:<br />';
echo "</div>\n";
echo "<input type='text' value='[br]'/></a><br />Перенос строки<br />\n";
echo "<input type='text' value='[b]ваш текст[/b]'/></a><br /><strong>-Жирный</strong><br />\n";
echo "<input type='text' value='[i]ваш текст[/i]' /></a><br /><em>-Курсив</em><br />\n";
echo "<input type='text' value='[c]ваш текст[/c]' /></a><br /><center>-сообщение в центре</center><br />\n";
echo "<input type='text' value='[small]Ваш текст[/small]' /></a><br /><span style='font-size:small;'>Маленький</span><br />\n";
echo "<div class='foot'>\n";
echo 'Цвета текста:<br />';
echo "</div>\n";
echo "<input type='text' value='[red]ваш текст[/red]'/></a><font color='red'>Красный</font><br /><br />\n";
echo "<input type='text' value='[green]ваш текст[/green]' /></a><font color='green'>Зеленый</font><br /><br />\n";
echo "<input type='text' value='[blue]ваш текст[/blue]' /></a><font color='blue'>Синий</font><br /><br />\n";
echo "<input type='text' value='[yellow]ваш текст[/yellow]' /></a><font color='yellow'>Желтый</font><br /><br />\n";
echo "<input type='text' value='[white]ваш текст[/white]' /></a><font color='white'>Белый</font><br /><br />\n";
echo "<div class='foot'>\n";
echo 'Ссылки:<br />';
echo "</div>\n";
echo "<input type='text' value='[url=http://адрес]название[/url]' /></a><br /><a href='bb-code.php'>Ссылка</a><br />\n";
echo "<input type='text' value='[u=id пользователя]ник пользователя[/u]'/></a><br /><a href='/info.php?id=1'>Пользователь</a><br />\n";

include_once '../../sys/inc/tfoot.php';
?>

<?
if (file_exists(H."style/themes/$set[set_them]/foot.php"))
include_once H."style/themes/$set[set_them]/foot.php";
else
{
	list($msec, $sec) = explode(chr(32), microtime());
	echo "<div class='foot'>";
	echo "<a href='/'>На главную</a><br />\n";

	echo "<a href='/users.php'>Регистраций: ".mysql_result(mysql_query("SELECT COUNT(*) FROM `user`"), 0)."</a><br />\n";
	echo "<a href='/online.php'>Сейчас на сайте: ".mysql_result(mysql_query("SELECT COUNT(*) FROM `user` WHERE `date_last` > ".(time()-600).""), 0)."</a><br />\n";
	echo "<a href='/online_g.php'>Гостей на сайте: ".mysql_result(mysql_query("SELECT COUNT(*) FROM `guests` WHERE `date_last` > ".(time()-600)." AND `pereh` > '0'"), 0)."</a><br />\n";

	$page_size = ob_get_length();
	ob_end_flush();
	if(!isset($_SESSION['traf']))
		$_SESSION['traf'] = 0;
		$_SESSION['traf'] += $page_size;

	echo '
		Вес страницы: '.round($page_size / 1024, 2).' Кб<br />
		Ваш трафик: '.round($_SESSION['traf'] / 1024, 2).' Кб <br />
		Генерация страницы: '.round(($sec + $msec) - $conf['headtime'], 3).'сек' ;
	echo "</div>\n";
	echo "<div class='rekl'>\n";
	rekl(3);
	echo "</div>\n";
	echo "</div>\n</body>\n</html>";
}
exit;
?>
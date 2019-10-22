<?
include_once '../sys/inc/start.php';
//include_once '../sys/inc/compress.php'; // если раскомментировать то файл будет качаться некорректно
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';
include_once '../sys/inc/downloadfile.php';


if (isset($_GET['id']) && mysql_result(mysql_query("SELECT COUNT(*) FROM `forum_files` WHERE `id` = '".intval($_GET['id'])."'"),0)==1)
{
$file=mysql_fetch_assoc(mysql_query("SELECT * FROM `forum_files` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1"));if (is_file(H.'sys/forum/files/'.$file['id'].'.frf') && isset($user) && $user['level']>=1 && isset($_GET['del']))
{



if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']!=NULL)
$link =$_SERVER['HTTP_REFERER'];
else
$link='/index.php';
mysql_query("DELETE FROM `forum_files` WHERE `id` = '$file[id]' LIMIT 1");
unlink(H.'sys/forum/files/'.$file['id'].'.frf');


if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']!=NULL)
header("Location: $_SERVER[HTTP_REFERER]");
else
header("Location: /forum/index.php?".SID);



}
elseif (is_file(H.'sys/forum/files/'.$file['id'].'.frf'))
{
mysql_query("UPDATE `forum_files` SET `count` = '".($file['count']+1)."' WHERE `id` = '$file[id]' LIMIT 1");
DownloadFile(H.'sys/forum/files/'.$file['id'].'.frf', $file['name'].'.'.$file['ras'],ras_to_mime($file['ras']));
exit;
}

}
else
{
header("Refresh: 3; url=/index.php");
header("Content-type: text/html",NULL,404);
echo "<html>
<head>
<title>Ошибка 404</title>\n";
echo "<link rel=\"stylesheet\" href=\"/style/themes/default/style.css\" type=\"text/css\" />\n";
echo "</head>\n<body>\n<div class=\"body\"><div class=\"err\">\n";

echo "Нет такой страницы\n";
echo "<br />";
echo "<a href=\"/index.php\">На главную</a>";
echo "</div>\n</div>\n</body>\n</html>";
exit;
}



?>
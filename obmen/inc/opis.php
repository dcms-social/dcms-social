<?
if (is_file("inc/opis/$ras.php"))include "inc/opis/$ras.php";
else
{
echo 'Размер: '.size_file($size)."<br />\n";
$ank=mysql_fetch_assoc(mysql_query("SELECT * FROM `user` WHERE `id` = '$post[id_user]' LIMIT 1"));
echo "Выгрузил: <a href='/info.php?id=$ank[id]'>$ank[nick]</a> ".vremja($post['time'])." <br />\n";
}
?>
<?
$gallery_q=mysql_query("SELECT * FROM `gallery` WHERE `id_user` = '$ank[id]'");
while ($gallery = mysql_fetch_assoc($gallery_q))
{
$q=mysql_query("SELECT * FROM `gallery_foto` WHERE `id_gallery` = '$gallery[id]'");
while ($post = mysql_fetch_assoc($q))
{
@unlink(H."sys/gallery/48/$post[id].jpg");
@unlink(H."sys/gallery/128/$post[id].jpg");
@unlink(H."sys/gallery/640/$post[id].jpg");
@unlink(H."sys/gallery/foto/$post[id].jpg");

mysql_query("DELETE FROM `gallery_foto` WHERE `id` = '$post[id]' LIMIT 1");
mysql_query("DELETE FROM `gallery_komm` WHERE `id_foto` = '$post[id]'");
mysql_query("DELETE FROM `gallery_rating` WHERE `id_foto` = '$post[id]'");
}

}
mysql_query("DELETE FROM `gallery` WHERE `id_user` = '$ank[id]'");
mysql_query("DELETE FROM `gallery_komm` WHERE `id_user` = '$ank[id]'");

if (isset($_GET['all']) && count($collisions)>1)
{
for ($i=1;$i<count($collisions);$i++)
{

$gallery_q=mysql_query("SELECT * FROM `gallery` WHERE `id_user` = '$collisions[$i]'");
while ($gallery = mysql_fetch_assoc($gallery_q))
{
$q=mysql_query("SELECT * FROM `gallery_foto` WHERE `id_gallery` = '$gallery[id]'");
while ($post = mysql_fetch_assoc($q))
{
@unlink(H."sys/gallery/48/$post[id].jpg");
@unlink(H."sys/gallery/128/$post[id].jpg");
@unlink(H."sys/gallery/640/$post[id].jpg");
@unlink(H."sys/gallery/foto/$post[id].jpg");

mysql_query("DELETE FROM `gallery_foto` WHERE `id` = '$post[id]' LIMIT 1");
mysql_query("DELETE FROM `gallery_komm` WHERE `id_foto` = '$post[$i]'");
mysql_query("DELETE FROM `gallery_rating` WHERE `id_foto` = '$post[$i]'");
}

}
mysql_query("DELETE FROM `gallery` WHERE `id_user` = '$collisions[$i]'");
}

}

?>
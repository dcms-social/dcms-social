<?
mysql_query("DELETE FROM `user_voice2` WHERE `id_user` = '$ank[id]' OR `id_kont` = '$ank[id]'");

if (isset($_GET['all']) && count($collisions)>1)
{
for ($i=1;$i<count($collisions);$i++)
{
mysql_query("DELETE FROM `user_voice2` WHERE `id_user` = '$collisions[$i]' OR `id_kont` = '$collisions[$i]'");
}

}
?>
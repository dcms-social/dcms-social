<?
function avatar($ID, $link = false, $dir = '50', $w = '50')
{
	/**
	* 
	* @var / Аватар, модифицировали функцию с целью облегчения кода
	* 
	*/
	$avatar = mysql_fetch_array(mysql_query("SELECT id,id_gallery,ras FROM `gallery_foto` WHERE `id_user` = '$ID' AND `avatar` = '1' LIMIT 1"));

	if (is_file(H."sys/gallery/$dir/$avatar[id].$avatar[ras]"))
	{
		return ($link == true ? '<a href="/foto/' . $ID . '/' . $avatar['id_gallery'] . '/' . $avatar['id'] . '/">' : false) . '
	<img class="avatar" src="/foto/foto' . $dir . '/' . $avatar['id'] . '.' . $avatar['ras'] . '" alt="Avatar"  width="' . $w . '" />' . ($link == true ? '</a>' : false);
	}
	else
	{
		return '<img class="avatar" src="/style/user/avatar.gif" width="' . $w . '" alt="No Avatar" />';
	}
	
}
?>
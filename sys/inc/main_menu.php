<?
$q_menu = mysql_query("SELECT * FROM `menu` ORDER BY `pos` ASC");

while ($post_menu = mysql_fetch_assoc($q_menu))
{
	if ($post_menu['type'] == 'link')
	echo '<div class="main_menu">';

	if ($post_menu['type'] == 'link')
	echo '<img src="/style/icons/' . $post_menu['icon'] . '" alt="*" /> ';

	if ($post_menu['type'] == 'link')
	echo '<a href="' . $post_menu['url'] . '">';
	else 
	echo '<div class="menu_razd">';
	
	echo $post_menu['name'];
	
	if ($post_menu['type'] == 'link')
	echo '</a> ';
	
	if ($post_menu['counter'] != NULL && is_file(H . $post_menu['counter']))
	{
		@include H . $post_menu['counter'];
	}

	echo '</div>';
}

if (user_access('adm_panel_show'))
{
	?>
	<div class="main2">
	<img src="/style/icons/adm.gif" alt="DS" /> <a href="/plugins/admin/">Админ кабинет</a> 
	<?
	include_once H.'plugins/admin/count.php';
	?>
	</div>
	<?
}
?>
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

only_reg();
$set['title']='Моя анкета';
include_once '../../sys/inc/thead.php';
title();


if (isset($_POST['save'])){

if (isset($_POST['stena_foto']) && $_POST['stena_foto']==0)
{
$user['stena_foto']=0;
mysql_query("UPDATE `user` SET `stena_foto` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['stena_foto']=1;
mysql_query("UPDATE `user` SET `stena_foto` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}



if (!isset($err))msg('Изменения успешно приняты');

}
err();
aut();


echo "<div id='comments' class='menu'>";
echo "<div class='webmenu'>";
        
echo "<a href='settings.php'>Общие</a>";

echo "</div>"; 

        
echo "<div class='webmenu last'>";
        
echo "<a href='stena.php' class='activ'>Стена</a>";

echo "</div>"; 
echo "</div>";
echo "<form method='post' action='?$passgen'>";
	
		
echo "

<label><input type='checkbox' name='stena_foto'".($user['stena_foto']==0?' checked="checked"':null)." value='0' /> Фотографии</label><br />


	<input type='submit' name='save' value='Сохранить' />
	</form>
	<div class='foot'>
	&raquo;<a href='anketa.php'>Посмотреть анкету</a><br />\n";


if(isset($_SESSION['refer']) && $_SESSION['refer']!=NULL && otkuda($_SESSION['refer']))
echo "&laquo;<a href='$_SESSION[refer]'>".otkuda($_SESSION['refer'])."</a><br />\n";

echo "&laquo;<a href='/umenu.php'>Мое меню</a><br /></div>\n";

	
include_once '../../sys/inc/tfoot.php';
?>
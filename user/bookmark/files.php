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











if (isset($user))$ank['id'] = $user['id'];



if (isset($_GET['id']))$ank['id'] = intval($_GET['id']);



$ank = get_user($ank['id']);







if ($ank['id'] == 0)



{



	header("Location: /index.php?" . SID);exit;



	exit;



}







if (isset($user) && isset($_GET['delete']) && $user['id'] == $ank['id'])



{



mysql_query("DELETE FROM `bookmarks` WHERE `id` = '" . intval($_GET['delete']) . "' AND `id_user` = '$user[id]' AND `type`='file' LIMIT 1");



	



	$_SESSION['message'] = 'Закладка удалена';



	header("Location: ?page=" . intval($_GET['page']) . "" . SID);exit;



	exit;



}







if( !$ank ){ header("Location: /index.php?".SID); exit; }



$set['title']='Закладки - Файлы - '. $ank['nick'] .''; // заголовок страницы







include_once '../../sys/inc/thead.php';



title();











err();



aut(); // форма авторизации







echo '<div class="foot">';



echo '<img src="/style/icons/str2.gif" alt="*" /> <a href="/user/bookmark/index.php?id=' . $ank['id'] . '">Закладки</a> | <b>Файлы</b>';



echo '</div>';











$k_post=mysql_result(mysql_query("SELECT COUNT(id_file) FROM `bookmarks` WHERE `id_user` = '$ank[id]' AND `type`='file'"),0);



$k_page=k_page($k_post,$set['p_str']);



$page=page($k_page);



$start=$set['p_str']*$page-$set['p_str'];



echo '<table class="post">';







if ($k_post == 0)



{



	echo '<div class="mess">';



	echo 'Нет файлов в закладках';



	echo '</div>';



}







$q=mysql_query("SELECT id_file,id FROM `bookmarks`  WHERE `id_user` = '$ank[id]' AND `type`='file' ORDER BY id DESC LIMIT $start, $set[p_str]");



while ($post = mysql_fetch_assoc($q))



{	



/*-----------зебра-----------*/ 



if ($num==0){



	echo '<div class="nav1">';



	$num=1;



}



elseif ($num==1){



	echo '<div class="nav2">';



	$num=0;



}



/*---------------------------*/











	$f = $post['id_object'];



	$file_id = mysql_fetch_assoc(mysql_query("SELECT id_dir,id,name,ras  FROM `obmennik_files` WHERE `id` = '" . $f . "'  LIMIT 1"));



	$dir = mysql_fetch_array(mysql_query("SELECT `dir` FROM `obmennik_dir` WHERE `id` = '$file_id[id_dir]' LIMIT 1"));







	echo '<a href="/obmen' . $dir['dir'] . $file_id['id'] . '.' . $file_id['ras'] . '?showinfo">' . htmlspecialchars($file_id['name']) . '.' . $file_id['ras'] . '</a>';







if ($ank['id'] == $user['id'])



echo '<div style="text-align:right;"><a href="?delete=' . $post['id'] . '&amp;page=' . $page . '"><img src="/style/icons/delete.gif" alt="*" /></a></div>';







	echo '</div>';



}



echo '</table>';



















if ($k_page>1)str('?',$k_page,$page); // Вывод страниц











echo '<div class="foot">';



echo '<img src="/style/icons/str2.gif" alt="*" /> <a href="/user/bookmark/index.php?id=' . $ank['id'] . '">Закладки</a> | <b>Файлы</b>';



echo '</div>';











include_once '../../sys/inc/tfoot.php';



?>




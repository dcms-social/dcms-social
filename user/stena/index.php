<?




$set['p_str']=5;




if (isset($_GET['likepost']))




{




$stena=mysql_fetch_assoc(mysql_query("SELECT * FROM `stena` WHERE `id` = '".intval($_GET['likepost'])."' LIMIT 1"));




$ank3=get_user($stena['id_user']);




$l=mysql_result(mysql_query("SELECT COUNT(*) FROM `stena_like` WHERE `id_stena` = '$stena[id]'"),0);




if (isset($_GET['likepost']) && mysql_result(mysql_query("SELECT COUNT(*) FROM `stena_like` WHERE




 `id_stena` = '$stena[id]' AND `id_user` = '$user[id]' LIMIT 1"),0)==0){




mysql_query("INSERT INTO `stena_like` (`id_user`, `id_stena`) values('$user[id]', '$stena[id]')");




mysql_query("UPDATE `user` SET `balls` = '".($ank3['balls']+1)."' WHERE `id` = '$ank3[id]' LIMIT 1");




}




}









$k_post=mysql_result(mysql_query("SELECT COUNT(*) FROM `stena` WHERE `id_stena` = '$ank[id]'"),0);




$k_page=k_page($k_post,$set['p_str']);




$page=page($k_page);




$start=$set['p_str']*$page-$set['p_str'];














if ($k_post==0)




{




echo "  <div class='mess'>\n";




echo "Нет сообщений\n";




echo "  </div>\n";




}




else




{




/*------------сортировка по времени--------------*/




if (isset($user)){




echo "<div id='comments' class='menus'>";




echo "<div class='webmenu'>";




echo "<a href='/info.php?id=$ank[id]&amp;page=$page&amp;sort=1' class='".($user['sort']==1?'activ':'')."'>Внизу</a>";




echo "</div>"; 




echo "<div class='webmenu'>";




echo "<a href='/info.php?id=$ank[id]&amp;page=$page&amp;sort=0' class='".($user['sort']==0?'activ':'')."'>Вверху</a>";




echo "</div>"; 




echo "</div>";




}




/*---------------alex-borisi---------------------*/




}









$q=mysql_query("SELECT * FROM `stena` WHERE `id_stena` = '$ank[id]' ORDER BY id $sort LIMIT $start, $set[p_str]");




$num=0;




while ($post = mysql_fetch_assoc($q))




{









/*-----------зебра-----------*/




if ($num==0)




{echo "  <div class='nav1'>\n";




$num=1;




}elseif ($num==1)




{echo "  <div class='nav2'>\n";




$num=0;}




/*---------------------------*/









$ank_stena=mysql_fetch_assoc(mysql_query("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));









if ($set['set_show_icon']==2){









avatar($ank_stena['id']);









}




elseif ($set['set_show_icon']==1)




{









echo "".group($ank_stena['id'])."";









}














echo "<a href='/info.php?id=$ank_stena[id]'>$ank_stena[nick]</a>\n";




echo "".medal($ank_stena['id'])." ".online($ank_stena['id'])."";




if (isset($user))echo " <a href='/info.php?id=$ank[id]&amp;response=$ank_stena[id]'>[*]</a>";




echo " (".vremja($post['time']).")<br />";





echo stena($ank_stena['id'],$post['id']).' <br/>';



echo output_text($post['msg'])."<br />\n";









if (isset($user))




{




$l=mysql_result(mysql_query("SELECT COUNT(*) FROM `stena_like` WHERE `id_stena` = '$post[id]'"),0);




echo '<a href="/user/komm.php?id='.$post['id'].'"><img src="/style/icons/uv.png"> ('.mysql_result(mysql_query("SELECT COUNT(*) FROM `stena_komm` WHERE `id_stena` = '$post[id]'"),0).') </a><span style="float:right;"> <a href="?id='.$ank['id'].'&amp;likepost='.$post['id'].'&amp;page='.$page.'" >&hearts; '.$l.'</a> ';









if (isset($user) && $ank_stena['id']!=$user['id'])echo "<a href=\"/info.php?id=$ank[id]&amp;page=$page&amp;spam=$post[id]\"><img src='/style/icons/blicon.gif' alt='*' title='Это спам'></a>"; 









if (user_access('guest_delete') || $ank['id']==$user['id'])




{




echo "<a href='?id=$ank[id]&amp;delete_post=$post[id]'><img src='/style/icons/delete.gif' alt='удалить' /></a>\n";




}




echo "   </span>\n";




}




echo "</div>\n";




}














if ($k_page>1)str('?id='.$ank['id'].'&',$k_page,$page); // Вывод страниц









if (isset($user) || (isset($set['write_guest']) && $set['write_guest']==1 && (!isset($_SESSION['antiflood']) || $_SESSION['antiflood']<$time-300)))




{




echo "<form method=\"post\" name='message' action=\"?id=$ank[id]$go_otv\">\n";




if ($set['web'] && is_file(H.'style/themes/'.$set['set_them'].'/altername_post_form.php'))




include_once H.'style/themes/'.$set['set_them'].'/altername_post_form.php';




else




echo "$tPanel<textarea name=\"msg\">$otvet</textarea><br />\n";




echo "<input value=\"Отправить\" type=\"submit\" />\n";




echo "</form><table width='99%'>\n";




}









?>




<?php
 /*
 ==========
 Вывод активности на стене
 ==========
 */
 function stena($id_us=NULL,$id=NULL){
 global $webbrowser;
 $ank_stena=mysql_fetch_assoc(mysql_query("SELECT `id`,`pol` FROM `user` WHERE `id`='".$id_us."' LIMIT 1")); //Определяем автора комментария
 if(mysql_result(mysql_query("SELECT COUNT(`id`)FROM `stena` WHERE `id`='".$id."' LIMIT 1"),0)==1){ //Если комментарий с такой записью существует, то...
 $post=mysql_fetch_assoc(mysql_query("SELECT * FROM `stena` WHERE `id`='".$id."' LIMIT 1"));
 if ($post)
 {
 if($post['type']=='foto'){ //Если смена аватара
 echo " <span style='color:darkgreen;'>установил".($ank_stena['pol']==0 ? 'а' : null)." новый аватар на своей страничке</span><br/>\n";
 $foto=mysql_fetch_assoc(mysql_query("SELECT `id`,`id_gallery`,`ras` FROM `gallery_foto` WHERE `id`='".$post['info_1']."' LIMIT 1"));
 echo "<a href='/foto/".$ank_stena['id']."/".$foto['id_gallery']."/".$foto['id']."/'><img class='stenka' style='width:".($webbrowser ? '240px;' : '60px;')."' src='/foto/foto0/".$foto['id'].".".$foto['ras']."'></a>";
 }elseif($post['type']=='note'){ //Если новый дневник
 $note=mysql_query("SELECT `id`,`name`,`msg` FROM `notes` WHERE `id`='".$post['info_1']."' LIMIT 1");
 if(mysql_num_rows($note)==0){ //Если такого дневника не существует, то...
 echo " <span style='color:#666;'>написал".($ank_stena['pol']==0 ? 'a' : null)." новый дневник, который был удалён.</span>";
 }else{ //А, если существует, то...
 $notes=mysql_fetch_assoc($note);
 echo " <span style='color:darkgreen;'>создал".($ank_stena['pol']==0 ? 'a' : null)." новую запись у себя в дневнике</span><br/>\n";
 echo "<a href='/plugins/notes/list.php?id=".$notes['id']."'><b style='color:#999;'>".text($notes['name'])."</b></a><br/>";
 echo '<span style="color:#666;">'.rez_text($notes['msg'],82).'</span>';
 }
 }elseif($post['type']=='them'){ //Если это тема форума
 $dump=mysql_query("SELECT `id`,`id_forum`,`id_razdel`,`name`,`text` FROM `forum_t` WHERE `id`='".$post['info_1']."' LIMIT 1");
 if(mysql_num_rows($dump)==0){ //Если нет такой темы, то...
 echo " <span style='color:#666;'>написал".($ank_stena['pol']==0 ? 'a' : null)." тему в форуме, которая была удалена.</span>";
 }else{ //Если есть,  то...
 $them=mysql_fetch_assoc($dump);
 echo " <span style='color:darkgreen;'>создал".($ank_stena['pol']==0 ? 'a' : null)." новую тему в форуме</span><br/>";
 echo " <a href='/forum/".$them['id_forum']."/".$them['id_razdel']."/".$them['id']."/'><b style='color:#999;'>".text($them['name'])."</b></a><br/>";
 echo " <span style='color:#666;'>".rez_text($them['text'],82)."</span>";
 }
 }

 }
 }
 }
 
?>
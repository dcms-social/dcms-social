<div style="padding: 6px 10px;" class="foot"><a href="/plugins/notes/">
 <b>Дневники</b> <?=mysql_result(mysql_query("SELECT COUNT(`id`)FROM `notes`"),0);?>+<?=mysql_result(mysql_query("SELECT COUNT(`id`)FROM `notes` WHERE `time`>'".($time-86000)."'"),0);?></span></a></div><?
$q=mysql_query("SELECT * FROM `notes` ORDER BY `time` DESC LIMIT 3");
if(mysql_num_rows($q)==0){
?><div class="nav2" style="color:#666;">Записей нет</div><?php
}else{
while($post=mysql_fetch_assoc($q)){
$note_name = '<a href="/plugins/notes/list.php?id='.$post['id'].'"><span style="color:#06f">'.text($post['name']).'</span></a>';
$note_text =$post['msg'];
$count_comm =mysql_result(mysql_query("SELECT COUNT(`id`) FROM `notes_komm` WHERE `id_notes`='".$post['id']."'"),0);
echo "<div style='border-bottom:1px #d5dde5 solid;' class='nav2'>\n";
?><?=group($post['id_user']);?> 
<?=user::nick($post['id_user'],1,1,1);?> : <?=$note_name;?>
<br/>
<?=rez_text($note_text,80);?><br/><?php
echo ($post['share']==1 ? "(!!) <i>Репостнутая запись</i><br/>" : null);
?><img src="/style/icons/comm_num_gray.png"><?=$count_comm;?>
<span style="float:right;color:#666;"><small>
<?=vremja($post['time']);?></small></div><?php
}
}
?><div class='nav1'> 
<?php if(isset($user)){?><a href="/plugins/notes/add.php">Написать</a><? } ?><span style="float:right;"><a href="/plugins/notes/">
Все записи &rarr;</a></span><br/>
</div><?
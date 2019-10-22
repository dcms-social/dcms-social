<? 
include_once '../sys/inc/start.php'; 
if (isset($_GET['showinfo']) || !isset($_GET['f']) || isset($_GET['komm'])) 
include_once '../sys/inc/compress.php'; 
include_once '../sys/inc/sess.php'; 
include_once '../sys/inc/home.php'; 
include_once '../sys/inc/settings.php'; 
include_once '../sys/inc/db_connect.php'; 
include_once '../sys/inc/ipua.php'; 
include_once '../sys/inc/fnc.php'; 
include_once '../sys/inc/obmen.php'; 
include_once '../sys/inc/user.php'; 

/* Бан пользователя */  
if (mysql_result(mysql_query("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'files' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0)!=0)
{ 
    header('Location: /ban.php?'.SID); 
    exit; 
} 

/*--------------Сортировка файлов------------------*/ 
if (!isset($_SESSION['sort']))$_SESSION['sort'] = 0; 
if (isset($_GET['sort_files']) && $_GET['sort_files'] == 1) 
$_SESSION['sort'] = 1; 
elseif (isset($_GET['sort_files'])) 
$_SESSION['sort'] = 0; 
if ($_SESSION['sort'] == 1)$sort_files = "k_loads"; 
else $sort_files = "time"; 
/*---------------plugins-----------------------*/ 

if (isset($_GET['d']) && esc($_GET['d']) != NULL) {
    $l = preg_replace("#\.{2,}#", NULL, esc($_GET['d']));
    $l = preg_replace("#\./|/\.#", NULL, $l);
    $l = preg_replace("#(/){1,}#", "/", $l);
    $l = '/' . preg_replace("#(^(/){1,})|((/){1,}$)#", "", $l);
} else {
    $l = '/';
}

if ($l == '/') {
    $dir_id['upload'] = 0;
    $id_dir = 0;
    $l = '/';
} elseif (mysql_result(mysql_query("SELECT COUNT(*) FROM `obmennik_dir` WHERE `dir` = '/$l' OR `dir` = '$l/' OR `dir` = '$l' LIMIT 1"), 0) != 0) {
    $dir_id = mysql_fetch_assoc(mysql_query("SELECT * FROM `obmennik_dir` WHERE `dir` = '/$l' OR `dir` = '$l/' OR `dir` = '$l' LIMIT 1"));
    $id_dir = $dir_id['id'];
} else {
    $dir_id['upload'] = 0;
    $id_dir = 0;
    $l = '/';
}

if (isset($_GET['f'])) 
{ 
$f=esc(urldecode($_GET['f'])); 
$name=preg_replace('#.[^.]*$#', NULL, $f); // имя файла без расширения 
$ras=strtolower(preg_replace('#^.*.#', NULL, $f));$ras=str_replace('jad', 'jar', $ras); 

if (mysql_result(mysql_query("SELECT COUNT(`id`) FROM `obmennik_files` WHERE `id_dir` = '$id_dir' AND `id`='".intval($_GET['f'])."' LIMIT 1"),0)!=0)
{ 
$file_id=mysql_fetch_assoc(mysql_query("SELECT * FROM `obmennik_files` WHERE `id_dir` = '$id_dir' AND `id`='".intval($_GET['f'])."'"));$ras=$file_id['ras'];
$file=H."sys/obmen/files/$file_id[id].dat"; 
$name=$file_id['name']; 
$size=$file_id['size']; 

$file_id['name'] = str_replace('_', ' _', $file_id['name']); 

if (!isset($_GET['showinfo']) && !isset($_GET['komm']) && is_file(H.'sys/obmen/files/'.$file_id['id'].'.dat'))
{ 

    if ($ras == 'jar' && strtolower(preg_replace('#^.*.#', NULL, $f)) == 'jad') 
    { 
        include_once H.'sys/inc/zip.php'; 
        $zip=new PclZip(H.'sys/obmen/files/'.$file_id['id'].'.dat'); 
        $content = $zip->extract(PCLZIP_OPT_BY_NAME, "META-INF/MANIFEST.MF" ,PCLZIP_OPT_EXTRACT_AS_STRING); 
        $jad=preg_replace("#(MIDlet-Jar-URL:( )*[^(n|r)]*)#i", NULL, $content[0]['content']); 
        $jad=preg_replace("#(MIDlet-Jar-Size:( )*[^(n|r)]*)(n|r)#i", NULL, $jad); 
        $jad=trim($jad); 
        $jad.="rnMIDlet-Jar-Size: ".filesize(H.'sys/obmen/files/'.$file_id['id'].'.dat').""; 
        $jad.="rnMIDlet-Jar-URL: /obmen$dir_id[dir]$file_id[id].$file_id[ras]"; 
        $jad=br($jad,"rn"); 
        header('Content-Type: text/vnd.sun.j2me.app-descriptor'); 
        header('Content-Disposition: attachment; filename="'.$file_id['name'].'.jad";'); 
        echo $jad; 
        exit; 
    } 

    $avtor = get_user($file_id['id_user']); 
    if (isset($user) && $_SESSION['file_'.$file['id'].''] == 0) 
    @mysql_query("UPDATE `user` SET `rating_tmp` = '".($avtor['rating_tmp']+1)."' WHERE `id` = '$file_id[id_user]' LIMIT 1"); 

    $_SESSION['file_'.$file['id'].'']=1; 
    @mysql_query("UPDATE `obmennik_files` SET `k_loads` = '".($file_id['k_loads']+1)."' WHERE `id` = '$file_id[id]' LIMIT 1"); 

    include_once '../sys/inc/downloadfile.php'; 
    DownloadFile(H.'sys/obmen/files/'.$file_id['id'].'.dat', retranslit($file_id['name']).'_'.$_SERVER['HTTP_HOST'].'.'.$ras, ras_to_mime($ras));
    exit; 
} 
$avtor = get_user($file_id['id_user']); 

/*------------------------Моя музыка--------------------------*/ 
$music_people = mysql_result(mysql_query("SELECT COUNT(*) FROM `user_music` WHERE `dir` = 'obmen' AND `id_file` = '$file_id[id]'"),0);
if (isset($user)) 
$music = mysql_result(mysql_query("SELECT COUNT(*) FROM `user_music` WHERE `id_user` = '$user[id]' AND `dir` = 'obmen' AND `id_file` = '$file_id[id]'"),0);

if (isset($user) && isset($_GET['play']) && ($_GET['play'] == 1 || $_GET['play'] == 0) && ($file_id['ras'] == 'mp3' || $file_id['ras'] == 'wav' || $file_id['ras'] == 'ogg'))
{ 
    if ($_GET['play'] == 1 && $music == 0) // Добавляем в плейлист 
    { 
    mysql_query("INSERT INTO `user_music` (`id_user`, `id_file`, `dir`) VALUES ('$user[id]', '$file_id[id]', 'obmen')");
    mysql_query("UPDATE `user` SET `balls` = '".($avtor['balls']+1)."', `rating_tmp` = '".($avtor['rating_tmp']+1)."' WHERE `id` = '$avtor[id]' LIMIT 1"); 
    $_SESSION['message']='Трек добавлен в плейлист'; 
    } 
     
    if ($_GET['play'] == 0 && $music == 1) // Удаляем из плейлиста 
    { 
    mysql_query("DELETE FROM `user_music` WHERE `id_user` = '$user[id]' AND `id_file` = '$file_id[id]' AND `dir` = 'obmen' LIMIT 1");
    mysql_query("UPDATE `user` SET `rating_tmp` = '".($avtor['rating_tmp']-1)."' WHERE `id` = '$avtor[id]' LIMIT 1"); 
     
    $_SESSION['message']='Трек удален из плейлиста'; 
    } 
    header ("Location: ?showinfo"); 
    exit; 
} 
/*------------------------------------------------------------*/ 

if (isset($_GET['fav']) && isset($user)){ 

    if (mysql_result(mysql_query("SELECT COUNT(*) FROM `bookmarks` WHERE `id_user` = '" . $user['id'] . "' AND `id_object` = '" . $file_id['id'] . "' AND `type`='file' LIMIT 1"),0)  ==  0 && $_GET['fav']  ==  1){ 
        mysql_query("INSERT INTO `bookmarks` (`type`,`id_object`, `id_user`, `time`) VALUES ('file','$file_id[id]', '$user[id]', '$time')");
        $_SESSION['message'] = text($file_id['name']) . ' добавлен в закладки'; 
    } 

    if (mysql_result(mysql_query("SELECT COUNT(*) FROM `bookmarks` WHERE `id_user` = '" . $user['id'] . "' AND `id_object` = '" . $file_id['id'] . "' AND `type`='file' LIMIT 1"),0)  ==  1 && $_GET['fav']  ==  0){ 
        mysql_query("DELETE FROM `bookmarks` WHERE `id_user` = '$user[id]' AND  `id_object` = '$file_id[id]' AND `type`='file'");
        $_SESSION['message'] = text($file_id['name']) . ' удален из закладок'; 
    } 
     
        header("Location: ?showinfo"); 
        exit; 
} 

/*------------------------Мне нравится------------------------*/ 
if (isset($user) && $avtor['id']!=$user['id'] && isset($_GET['like']) && ($_GET['like'] == 1 || $_GET['like'] == 0) && mysql_result(mysql_query("SELECT COUNT(*) FROM `like_object` WHERE `id_object` = '$file_id[id]' AND `type` = 'obmen' AND `id_user` = '$user[id]'"),0) == 0)
{ 
mysql_query("INSERT INTO `like_object` (`id_user`, `id_object`, `type`, `like`) VALUES ('$user[id]', '$file_id[id]', 'obmen', '".intval($_GET['like'])."')");
mysql_query("UPDATE `user` SET `balls` = '".($avtor['balls']+1)."', `rating_tmp` = '".($avtor['rating_tmp']+1)."' WHERE `id` = '$avtor[id]' LIMIT 1"); 

} 
/*------------------------------------------------------------*/ 

$set['title']='Обменник - ' . text(str_replace('_', ' _', $file_id['name'])); // заголовок страницы
include_once '../sys/inc/thead.php'; 
title(); 


if (isset($_GET['spam'])  && isset($user)) 
{ 
    $mess = mysql_fetch_assoc(mysql_query("SELECT * FROM `obmennik_komm` WHERE `id` = '".intval($_GET['spam'])."' limit 1"));
    $spamer = get_user($mess['id_user']); 
     
    if (mysql_result(mysql_query("SELECT COUNT(*) FROM `spamus` WHERE `id_user` = '$user[id]' AND `id_spam` = '$spamer[id]' AND `razdel` = 'obmen_komm' AND `spam` = '".$mess['msg']."'"),0) == 0)
    { 
        if (isset($_POST['msg'])) 
        { 
            if ($mess['id_user']!=$user['id']) 
            { 
                $msg = mysql_real_escape_string($_POST['msg']); 

                if (strlen2($msg) < 3)$err = 'Укажите подробнее причину жалобы'; 
                if (strlen2($msg) > 1512)$err = 'Длина текста превышает предел в 512 символов'; 

                if(isset($_POST['types'])) $types = intval($_POST['types']); 
                else $types = '0';  
                if (!isset($err)) 
                { 
                    mysql_query("INSERT INTO `spamus` (`id_object`, `id_user`, `msg`, `id_spam`, `time`, `types`, `razdel`, `spam`) values('$file_id[id]', '$user[id]', '$msg', '$spamer[id]', '$time', '$types', 'obmen_komm', '".my_esc($mess['msg'])."')");
                    $_SESSION['message'] = 'Заявка на рассмотрение отправлена';  
                    header("Location: /obmen$dir_id[dir]$file_id[id].$file_id[ras]?showinfo&spam=$mess[id]&page=".intval($_GET['page']).""); 
                    exit; 
                } 
            } 
        } 
    } 
aut(); 
err(); 

if (mysql_result(mysql_query("SELECT COUNT(*) FROM `spamus` WHERE `id_user` = '$user[id]' AND `id_spam` = '$spamer[id]' AND `razdel` = 'obmen_komm'"),0) == 0)
{ 
    echo "<div class='mess'>Ложная информация может привести к блокировке ника.  
    Если вас постоянно достает один человек - пишет всякие гадости, вы можете добавить его в черный список.</div>"; 
    echo "<form class='nav1' method='post' action='/obmen$dir_id[dir]$file_id[id].$file_id[ras]?showinfo&spam=$mess[id]&page=".intval($_GET['page'])."'>\n"; 
    echo "<b>Пользователь:</b> "; 
    echo " ".status($spamer['id'])."  ".group($spamer['id'])." <a href='/info.php?id=$spamer[id]>$spamer[nick]</a>\n"; 
    echo "".medal($spamer['id'])." ".online($spamer['id'])." (".vremja($mess['time']).") "; 
    echo "<b>Нарушение:</b> <font color='green'>".output_text($mess['msg'])."</font> "; 
    echo "Причина:\n<select name='types'>\n"; 
    echo "<option value='1' selected='selected'>Спам/Реклама</option>\n"; 
    echo "<option value='2' selected='selected'>Мошенничество</option>\n"; 
    echo "<option value='3' selected='selected'>Оскорбление</option>\n"; 
    echo "<option value='0' selected='selected'>Другое</option>\n"; 
    echo "</select>\n"; 
    echo "Комментарий: "; 
    echo "<textarea name='msg'></textarea>"; 
    echo "<input value='Отправить' type='submit'/>\n"; 
    echo "</form>\n"; 
} else { 
    echo "<div class='mess'>Жалоба на <font color='green'>$spamer[nick]</font> будет рассмотрена в ближайшее время.</div>"; 
} 

echo "<div class='foot'>\n"; 
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/obmen$dir_id[dir]$file_id[id].$file_id[ras]?showinfo&page=".intval($_GET['page'])."'>Назад</a>\n"; 
echo "</div>\n"; 
include_once '../sys/inc/tfoot.php'; 
exit; 
} 



if (isset($user)) 
mysql_query("UPDATE `notification` SET `read` = '1' WHERE `type` = 'obmen_komm' AND `id_user` = '$user[id]' AND `id_object` = '$file_id[id]'");

if (isset($_POST['msg']) && isset($user)) 
{ 
    $msg=$_POST['msg']; 
    if (isset($_POST['translit']) && $_POST['translit'] == 1)$msg=translit($msg); 

    $mat=antimat($msg); 
    if ($mat)$err[]='В тексте сообщения обнаружен мат: '.$mat; 

    if (strlen2($msg)>1024){$err[]='Сообщение слишком длинное';} 
    elseif (strlen2($msg)<2){$err[]='Короткое сообщение';} 
    elseif (mysql_result(mysql_query("SELECT COUNT(*) FROM `obmennik_komm` WHERE `id_file` = '$file_id[id]' AND `id_user` = '$user[id]' AND `msg` = '".mysql_escape_string($msg)."' LIMIT 1"),0)!=0){$err='Ваше сообщение повторяет предыдущее';} 
    elseif(!isset($err)){ 

    $ank=get_user($file_id['id_user']); 

            if (isset($user) && $respons == TRUE){ 
            $notifiacation=mysql_fetch_assoc(mysql_query("SELECT * FROM `notification_set` WHERE `id_user` = '".$ank_otv['id']."' LIMIT 1"));
                 
                if ($notifiacation['komm']  ==  1 && $ank_otv['id'] != $user['id']) 
                mysql_query("INSERT INTO `notification` (`avtor`, `id_user`, `id_object`, `type`, `time`) VALUES ('$user[id]', '$ank_otv[id]', '$file_id[id]', 'obmen_komm', '$time')");
             
            } 
             
    $q = mysql_query("SELECT * FROM `frends` WHERE `user` = '".$file_id['id_user']."' AND `i` = '1' AND `frend` != '$user[id]'");
    while ($f = mysql_fetch_array($q)) 
    { 
        $a=get_user($f['frend']); 
        $discSet = mysql_fetch_array(mysql_query("SELECT * FROM `discussions_set` WHERE `id_user` = '".$a['id']."' LIMIT 1")); // Общая настройка обсуждений

        if ($f['disc_forum'] == 1 && $discSet['disc_forum'] == 1) /* Фильтр рассылки */ 
        { 
            // друзьям автора 
            if (mysql_result(mysql_query("SELECT COUNT(*) FROM `discussions` WHERE `id_user` = '$a[id]' AND `type` = 'obmen' AND `id_sim` = '$file_id[id]' LIMIT 1"),0) == 0)
            { 
            if ($file_id['id_user']!=$a['id'] || $a['id'] != $user['id']) 
            mysql_query("INSERT INTO `discussions` (`id_user`, `avtor`, `type`, `time`, `id_sim`, `count`) values('$a[id]', '$file_id[id_user]', 'obmen', '$time', '$file_id[id]', '1')"); 
            } 
            else 
            { 
            $disc = mysql_fetch_array(mysql_query("SELECT * FROM `discussions` WHERE `id_user` = '$file_id[id_user]' AND `type` = 'obmen' AND `id_sim` = '$file_id[id]' LIMIT 1"));
            if ($file_id['id_user']!=$a['id'] || $a['id']!= $user['id']) 
            mysql_query("UPDATE `discussions` SET `count` = '".($disc['count']+1)."', `time` = '$time' WHERE `id_user` = '$a[id]' AND `type` = 'obmen' AND `id_sim` = '$file_id[id]' LIMIT 1");
            } 

        } 

    } 

    // отправляем автору 
    if (mysql_result(mysql_query("SELECT COUNT(*) FROM `discussions` WHERE `id_user` = '$file_id[id_user]' AND `type` = 'obmen' AND `id_sim` = '$file_id[id]' LIMIT 1"),0) == 0)
    { 
        if ($file_id['id_user'] != $user['id']) 
        mysql_query("INSERT INTO `discussions` (`id_user`, `avtor`, `type`, `time`, `id_sim`, `count`) values('$file_id[id_user]', '$file_id[id_user]', 'obmen', '$time', '$file_id[id]', '1')"); 
    } 
    else 
    { 
        $disc = mysql_fetch_array(mysql_query("SELECT * FROM `discussions` WHERE `id_user` = '$file_id[id_user]' AND `type` = 'obmen' AND `id_sim` = '$file_id[id]' LIMIT 1"));
        if ($file_id['id_user'] != $user['id']) 
        mysql_query("UPDATE `discussions` SET `count` = '".($disc['count']+1)."', `time` = '$time' WHERE `id_user` = '$file_id[id_user]' AND `type` = 'obmen' AND `id_sim` = '$file_id[id]' LIMIT 1");
    } 

    mysql_query("INSERT INTO `obmennik_komm` (`id_file`, `id_user`, `time`, `msg`) values('$file_id[id]', '$user[id]', '$time', '".my_esc($msg)."')");
    mysql_query("UPDATE `user` SET `balls` = '".($user['balls']+1)."', `rating_tmp` = '".($user['rating_tmp']+1)."' WHERE `id` = '$user[id]' LIMIT 1"); 
    $_SESSION['message']='Сообщение успешно добавлено'; 
    header ("Location: /obmen$dir_id[dir]$file_id[id].$file_id[ras]?showinfo"); 
    exit; 
    } 
} 

include 'inc/file_act.php'; 
err(); 
aut(); // форма авторизации 


$my_dir = mysql_fetch_assoc(mysql_query("SELECT * FROM `user_files` WHERE `id` = '$file_id[my_dir]' LIMIT 1"));


/*--------------------Папка под паролем--------------------*/ 
if ($my_dir['pass']!=NULL) 
{ 
    if (isset($_POST['password'])) 
    { 
        $_SESSION['pass']=my_esc($_POST['password']); 
         
        if ($_SESSION['pass']!=$my_dir['pass']) 
        { 
            $_SESSION['message'] = 'Неверный пароль';  
            $_SESSION['pass']=NULL; 
        } 
         
        header("Location: ?showinfo"); 
        exit; 
    } 

    if (!user_access('obmen_dir_edit') && ($user['id']!=$avtor['id'] && $_SESSION['pass']!=$my_dir['pass']))
    { 
        echo '<form action="?showinfo" method="POST">Пароль:  
        <input type="pass" name="password" value="" /> 
         
        <input type="submit" value="Войти"/></form>'; 
        include_once '../sys/inc/tfoot.php'; 
        exit; 
    } 
} 
/*---------------------------------------------------------*/ 

include_once 'inc/komm_act.php'; // действия с комментариями 
include 'inc/file_form.php'; 

echo '<div class="main">'; 

include_once 'inc/icon14.php'; 
echo output_text($file_id['name']).'.'.$ras.' '; 
if ($file_id['metka']  ==  1)echo ' <font color=red><b>(18+)</b></font>'; 
echo '</div>'; 

if (($user['abuld']  ==  1 || $file_id['metka']  ==  0 || $file_id['id_user']  ==  $user['id'])) // Метка 18+ 
{ 

echo '<div class="main">'; 
if(is_file("inc/file/$ras.php"))include "inc/file/$ras.php"; 
else 
include_once 'inc/file.php'; 
echo '</div>'; 

}elseif (!isset($user)){ 
echo '<div class="mess">'; 
echo '<img src="/style/icons/small_adult.gif" alt="*"> 
 Данный файл содержит изображения эротического характера. Только зарегистрированные пользователи старше 18 лет могут просматривать такие файлы. 
'; 
echo '<a href="/aut.php">Вход</a> | <a href="/reg.php">Регистрация</a>'; 
echo '</div>'; 
}else{ 
echo '<div class="mess">'; 
echo '<img src="/style/icons/small_adult.gif" alt="*"> 
  
    Данный файл содержит изображения эротического характера.  
    Если Вас это не смущает и Вам 18 или более лет, то можете <a href="/obmen'.$dir_id['dir'].$file_id['id'].'.'.$file_id['ras'].'?showinfo&sess_abuld=1">продолжить просмотр</a>.  
    Или Вы можете отключить предупреждения в <a href="/user/info/settings.php">настройках</a>.';
    echo '</div>'; 
} 

//----------------------листинг-------------------// 
$listr = mysql_fetch_assoc(mysql_query("SELECT * FROM `obmennik_files` WHERE `id_dir` = '$dir_id[id]' AND `id` < '$file_id[id]' ORDER BY `id` DESC LIMIT 1"));
$list = mysql_fetch_assoc(mysql_query("SELECT * FROM `obmennik_files` WHERE `id_dir` = '$dir_id[id]' AND `id` > '$file_id[id]' ORDER BY `id`  ASC LIMIT 1"));
echo '<div class="c2" style="text-align: center;">'; 
echo '<span class="page">'.($list['id']?'<a href="/obmen'.$dir_id['dir'] . $list['id'].'.'.$list['ras'].'?showinfo">&laquo; Пред.</a> ':'&laquo; Пред. ').'</span>'; 

$k_1=mysql_result(mysql_query("SELECT COUNT(*) FROM `obmennik_files` WHERE `id` > '$file_id[id]' AND `id_dir` = '$id_dir'"),0)+1;
$k_2=mysql_result(mysql_query("SELECT COUNT(*) FROM `obmennik_files` WHERE `id_dir` = '$id_dir'"),0);
echo ' ('.$k_1.' из '.$k_2.') '; 

echo '<span class="page">'.($listr['id']?'<a href="/obmen'.$dir_id['dir'] . $listr['id'].'.'.$listr['ras'].'?showinfo">След. &raquo;</a>':' След. &raquo;').'</span>'; 
echo '</div>'; 
//----------------------plugins---------------// 

if (($user['abuld']  ==  1 || $file_id['metka']  ==  0 || $file_id['id_user']  ==  $user['id'])) // Метка 18+ 
{ 
/*----------------Действия над файлом-------------*/ 
if (user_access('obmen_file_edit') || $user['id'] == $file_id['id_user']) 
{ 
    echo '<div class="main">'; 
    echo '<img src="/style/icons/edit.gif" alt="*"> <a href="?showinfo&act=edit">Редактировать</a>'; 
    echo '<br/><img src="/style/icons/delete.gif" alt="*"> <a href="?showinfo&act=delete">Удалить</a>'; 
    echo '</div>'; 
} 
//----------------------plugins---------------///*------------------Мне нравится------------------*/ 
echo '<div class="main">'; 
$l1=mysql_result(mysql_query("SELECT COUNT(*) FROM `like_object` WHERE `id_object` = '$file_id[id]' AND `type` = 'obmen' AND `like` = '1'"),0);
$l2=mysql_result(mysql_query("SELECT COUNT(*) FROM `like_object` WHERE `id_object` = '$file_id[id]' AND `type` = 'obmen' AND `like` = '0'"),0);
if (isset($user) && $avtor['id']!=$user['id'] && mysql_result(mysql_query("SELECT COUNT(*) FROM `like_object` WHERE `id_object` = '$file_id[id]' AND `type` = 'obmen' AND `id_user` = '$user[id]'"),0) == 0)
{ 
echo '<img src="/style/icons/thumbu.png" alt="*"> <a href="/obmen'.$dir_id['dir'].$file_id['id'].'.'.$file_id['ras'].'?showinfo&like=1">Мне нравится</a> ('.($l1-$l2).') '; 
echo '<a href="/obmen'.$dir_id['dir'].$file_id['id'].'.'.$file_id['ras'].'?showinfo&like=0"><img src="/style/icons/thumbd.png" alt="*"></a>'; 

}else{ 
echo '<img src="/style/icons/thumbu.png" alt="*"> ('.($l1-$l2).') ';
echo ' <img src="/style/icons/thumbd.png" alt="*"> ';
} 
echo '</div>'; 



if (isset($user)){ 
    $markinfo=mysql_result(mysql_query("SELECT COUNT(`id`) FROM `bookmarks` WHERE `id_object` = '" . $file_id['id'] . "' AND `type`='file'"),0);

    echo "<div class='main'>"; 
    echo "<img src='/style/icons/add_fav.gif' alt='*' /> "; 
    if (mysql_result(mysql_query("SELECT COUNT(`id`) FROM `bookmarks` WHERE `id_user` = '" . $user['id'] . "' AND `id_object` = '".$file_id['id']."' AND `type`='file' LIMIT 1"),0)  ==  0)
    echo "<a href='?showinfo&fav=1'>Добавить в закладки</a>\n"; 
    else 
    echo "<a href='?showinfo&fav=0'>Удалить из закладок</a>\n"; 
    echo "<br/><img src='/style/icons/add_fav.gif' alt='*' /'> В закладках у <a href='?showinfo&markinfo'>$markinfo</a> чел."; 
    echo "</div>"; 
} 

echo '<div class="main">'; 
if ($file_id['ras'] == 'jar') 
echo '<img src="/style/icons/d.gif" alt="*"> <a href="/obmen'.$dir_id['dir'].$file_id['id'].'.'.$file_id['ras'].'">Скачать JAR ('.size_file($size).')</a> <a href="/obmen'.$dir_id['dir'].$file_id['id'].'.jad">JAD</a>  
'; 
else 
echo '<img src="/style/icons/d.gif" alt="*"> <a href="/obmen'.$dir_id['dir'].$file_id['id'].'.'.$file_id['ras'].'">Скачать ('.size_file($size).')</a> 
'; 
echo '<br/>Скачан ('.$file_id['k_loads'].')'; 
echo '</div>'; 

} 

echo '<div class="main">'; 
echo 'Добавил: '; 
echo group($avtor['id']).' ';
echo user::nick($avtor['id'],1,1,1);
echo ' <span style="color:#666;">'.vremja($file_id['time']).'</span><br/>'; 
echo 'В папку: <a href="/user/personalfiles/'.$avtor['id'].'/'.$my_dir['id'].'/">'.text($my_dir['name']).'</a>';
echo '</div>'; 

/*-------------------Моя музыка---------------------*/ 
if (isset($user) && ($file_id['ras'] == 'mp3' || $file_id['ras'] == 'wav' || $file_id['ras'] == 'ogg')) 
{ 
echo '<div class="main">'; 
if ($music == 0) 
echo '<a href="?showinfo&play=1"><img src="/style/icons/play.png" alt="*"></a> ('.$music_people.')'; 
else 
echo '<a href="?showinfo&play=0"><img src="/style/icons/play.png" alt="*"></a> ('.$music_people.') <img src="/style/icons/ok.gif" alt="*">'; 
echo '</div>'; 
} 
/*--------------------------------------------------*/ 


$_SESSION['page']=1; 
include_once '../sys/inc/thead.php'; 

include_once 'inc/komm.php'; 

echo '<div class="foot">'; 
echo '<img src="/style/icons/str2.gif" alt="*"> <a href="/obmen'.$dir_id['dir'].'">В папку</a>
'; 
echo '</div>'; 

include_once '../sys/inc/tfoot.php'; 

} 
} 
include_once 'inc/dir.php'; 

include_once '../sys/inc/tfoot.php'; 
?>
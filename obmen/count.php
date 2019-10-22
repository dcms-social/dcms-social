<?$my_dir = mysql_fetch_assoc(mysql_query("SELECT * FROM `obmennik_dir` WHERE `my` = '1' LIMIT 1"));
$k_p=mysql_result(mysql_query("SELECT COUNT(*) FROM `obmennik_files` WHERE `id_dir` != '$my_dir[id]'",$db), 0);
$k_n= mysql_result(mysql_query("SELECT COUNT(*) FROM `obmennik_files` WHERE `id_dir` != '$my_dir[id]' AND `time_go` > '".$ftime."'",$db), 0);
if ($k_n==0)$k_n=NULL;
else $k_n='+'.$k_n;
echo "($k_p) <font color='red'>$k_n</font>";
?>

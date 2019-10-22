<?

$k_n= mysql_result(mysql_query("SELECT COUNT(*) FROM `adm_chat` WHERE `time` > '$ftime'",$db), 0);
if ($k_n==0)$k_n=NULL;
else $k_n='+'.$k_n;
echo " <font color='red'>$k_n</font> ";
?>

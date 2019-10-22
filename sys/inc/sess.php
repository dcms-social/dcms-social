<?
@session_name('SESS');
@session_start();
$sess = mysql_real_escape_string(session_id());

if (!preg_match('#[A-z0-9]{32}#i',$sess))
	$sess = md5(rand(09009,999999));
?>
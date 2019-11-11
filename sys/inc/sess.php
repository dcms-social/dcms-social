<?
@session_name('SESS');
@session_start();
$sess = addslashes(session_id());

if (!preg_match('#[A-z0-9]{32}#i',$sess))
	$sess = md5(rand(10000,999999));
?>

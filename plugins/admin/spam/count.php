<?
if ($user['group_access']==2)
{
$types = " where `types` = 'chat' ";
}
elseif ($user['group_access']==3)
{
$types =" where `types` = 'forum' ";
}
elseif ($user['group_access']==4)
{
$types = " where (`types` = 'obmen_komm' OR `types` = 'files_komm') ";
}
elseif ($user['group_access']==5)
{
$types = " where `types` = 'lib_komm' ";
}
elseif ($user['group_access']==6)
{
$types = " where `types` = 'foto_komm' ";
}
elseif ($user['group_access']==11)
{
$types = " where `types` = 'notes_komm' ";
}
elseif ($user['group_access']==12)
{
$types = " where `types` = 'guest' ";
}
elseif (($user['group_access']>6 && $user['group_access']<10) || $user['group_access']==15)
{
$types = null;
}
$k_p=mysql_result(mysql_query("SELECT COUNT(*) FROM `spamus` $types",$db), 0);

echo "($k_p)";
?>

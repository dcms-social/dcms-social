<?
// данный скрипт добавяет отсутствующие таблицы в базу данных
// также он используется для установки движка


$tab=mysql_query('SHOW TABLES');
while ($tables=mysql_fetch_array($tab)) {
$_ver_table[$tables[0]]=1;
}
$k_sql=0;
$ok_sql=0;
$opdirtables=opendir(H.'install/db_tables');
while ($filetables=readdir($opdirtables))
{
if (preg_match('#\.sql$#i',$filetables))
{
$table_name=preg_replace('#\.sql$#i',null,$filetables);
if (!isset($_ver_table[$table_name]))
{

include_once H.'sys/inc/sql_parser.php';
$sql=SQLParser::getQueriesFromFile(H.'install/db_tables/'.$filetables);


for ($i=0;$i<count($sql);$i++)
{
$k_sql++; // счетчик запросов (для установщика)
if (@mysql_query($sql[$i])) {
$ok_sql++; // счетчик успешно выполненных запросов (для установщика)
}
}
}
}
}
closedir($opdirtables);





if (!isset($install)){

// выполнение одноразовых запросов
$opdirtables=opendir(H.'install/update/');
while ($rd=readdir($opdirtables))
{
if (preg_match('#^\.#',$rd))continue;
if (isset($set['update'][$rd]))continue;

if (preg_match('#\.sql$#i',$rd))
{
include_once H.'sys/inc/sql_parser.php';
$sql=SQLParser::getQueriesFromFile(H.'install/update/'.$rd);
for ($i=0;$i<count($sql);$i++){mysql_query($sql[$i]);}
$set['update'][$rd]=true;
$save_settings=true;
}
elseif(preg_match('#\.php$#i',$rd))
{
include_once H.'install/update/'.$rd;
$set['update'][$rd]=true;
$save_settings=true;
}


}

closedir($opdirtables);

if (isset($save_settings))save_settings($set);
}

?>
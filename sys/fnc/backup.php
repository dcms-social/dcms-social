<?
if (!isset($hard_process))
{
	$q = mysql_query("SELECT * FROM `cron` WHERE `id` = 'backup_mysql'");

	if (mysql_num_rows($q) == 0)
		mysql_query("INSERT INTO `cron` (`id`, `time`) VALUES ('backup_mysql', '" . time() . "')");

	$backup = mysql_fetch_assoc($q);

	if (preg_match('#^[^@]*@[^@]*\.[^@]*$#iu',$set['mail_backup']) && ($backup['time'] == NULL || $backup['time'] < time()-60*60*24))
	{
		mysql_query("UPDATE `cron` SET `time` = '" . time() . "' WHERE `id` = 'backup_mysql'");
		$hard_process = true;

		if (function_exists('set_time_limit'))
		@set_time_limit(600); // Ставим ограничение на 10 минут

		@unlink(H."sys/tmp/MySQL.sql.gz");
		$list_tables = NULL;
		$tab = @mysql_list_tables($set['mysql_db_name']);

		for($i = 0; $i < mysql_num_rows($tab); $i++)
		{
			$sql = NULL;
			$table = mysql_tablename($tab,$i);
			$sql .= "DROP TABLE IF EXISTS `$table`;\r\n";
			$res = @mysql_query("SHOW CREATE TABLE `$table`");
			$row = @mysql_fetch_row($res);
			$sql .= $row[1].";\r\n\r\n";
			$res = @mysql_query("SELECT * FROM `$table`");

			if (@mysql_num_rows($res) > 0) 
			{
				while (($row = @mysql_fetch_assoc($res))) 
				{
					$keys = @implode("`, `", @array_keys($row));
					$values = @array_values($row);

					foreach($values as $k => $v) 
					{
						$values[$k] = my_esc($v);
						$values[$k] = preg_replace("(\n|\r)", '\n', $values[$k]);
					}

					$values2 = @implode("', '", $values);
					$values2 = "'" . $values2 . "'";
					$values2 = str_replace("''", "null", $values2);
					$sql  .=  "INSERT INTO `$table` (`$keys`) VALUES ($values2);\r\n";
				}
				
				$sql  .=  "\r\n\r\n";
			}

			$fopen_mysql = fopen(H."sys/tmp/MySQL.sql.gz",'a');

			if (strlen($sql) < 5 * 1024 * 1024)
			fwrite($fopen_mysql, gzencode($sql,9));
			fclose($fopen_mysql);
		}
		
		$EOL = "\r\n";
		$subj = 'BackUp DCMS-Social';
		$bound = "--".md5(uniqid(time()));
		$headers = "From: \"BackUP@$_SERVER[HTTP_HOST]\" <BackUp@$_SERVER[HTTP_HOST]>$EOL";
		$headers .= "To: $set[mail_backup]$EOL";
		$headers .= "Subject: $subj$EOL";
		$headers .= "Mime-Version: 1.0$EOL";
		$headers .= "Content-Type: multipart/mixed; boundary=\"$bound\"$EOL";
		$body = "--$bound$EOL";
		$body .= "Content-Type: text/plain; charset=\"utf-8\"$EOL";
		$body .= "Content-Transfer-Encoding: 8bit$EOL";
		$body .= $EOL;
		$body .= "Автоматическая отправка BackUp базы данных";
		$body .= "$EOL--$bound$EOL";
		$body .= "Content-Type: application/x-gzip; name=\"MySQL.sql.gz\"$EOL";
		$body .= "Content-Disposition: attachment; filename=\"MySQL.sql.gz\"$EOL";
		$body .= "Content-Transfer-Encoding: Base64$EOL";
		$body .= $EOL;
		$body .= chunk_split(base64_encode(file_get_contents(H."sys/tmp/MySQL.sql.gz")));
		$body .= "$EOL--$bound$EOL";
		$body .= "Content-Type: text/plain; name=\"settings_6.2.dat\"$EOL";
		$body .= "Content-Disposition: attachment; filename=\"settings_6.2.dat\"$EOL";
		$body .= "Content-Transfer-Encoding: Base64$EOL";
		$body .= $EOL;
		$body .= chunk_split(base64_encode(file_get_contents(H."sys/dat/settings_6.2.dat")));
		$body .= "$EOL--$bound--$EOL";
		mail("$set[mail_backup]", '=?utf-8?B?'.base64_encode($subj).'?=', $body, $headers);
		unlink(H."sys/tmp/MySQL.sql.gz");
	}
}
?>
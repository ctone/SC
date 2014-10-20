<?php
	require_once("dbconfig.php");

	function ExecNonQuery($sql)
	{
		$DB_HOST			= dbconfig::$DB_HOST;
		$DB_USER			= dbconfig::$DB_USER;
		$DB_PASSWORD		= dbconfig::$DB_PASSWORD;
		$DB_NAME			= dbconfig::$DB_NAME;
		$id = 0;
		$link = @mysql_connect($DB_HOST, $DB_USER, $DB_PASSWORD);
		if ($link == false) 
		{
			$ErrorMessage = mysql_error();
			WriteErrorLog($ErrorMessage, "MySQL Connect Error :");
			RedirectToMaintainPage();
		}
		$selectdb = @mysql_select_db($DB_NAME,$link);
		if ($selectdb == false) 
		{
			$ErrorMessage = mysql_error();
			WriteErrorLog($ErrorMessage, "MySQL Select Database Error :");
			RedirectToMaintainPage();
		}
		mysql_query("SET NAMES UTF8");
		date_default_timezone_set('Asia/Bangkok');
		$result = @mysql_query($sql, $link);
		if ($result == false)
		{
			$ErrorMessage = mysql_error() . " Query : " . $sql;
			WriteErrorLog($ErrorMessage, "SQL Query Error :");
			RedirectToMaintainPage();
		}
		$id = mysql_insert_id();
		mysql_close($link);
		return $id;
	}

	function ExecQuery($sql, $format="list")
	{
		$DB_HOST			= dbconfig::$DB_HOST;
		$DB_USER			= dbconfig::$DB_USER;
		$DB_PASSWORD		= dbconfig::$DB_PASSWORD;
		$DB_NAME			= dbconfig::$DB_NAME;
		$link = @mysql_connect($DB_HOST,$DB_USER,$DB_PASSWORD);
		if ($link == false) 
		{
			$ErrorMessage = mysql_error();
			WriteErrorLog($ErrorMessage, "MySQL Connect Database Error :");
			RedirectToMaintainPage();
		}
		$selectdb = @mysql_select_db($DB_NAME,$link);
		if ($selectdb == false) 
		{
			$ErrorMessage = mysql_error();
			WriteErrorLog($ErrorMessage, "MySQL Select Database Error :");
			RedirectToMaintainPage();
		}
		mysql_query("SET NAMES UTF8");
		date_default_timezone_set('Asia/Bangkok');
		$result = @mysql_query($sql, $link);
		if ($result == false)
		{
			$ErrorMessage = mysql_error() . " Query : " . $sql;
			WriteErrorLog($ErrorMessage, "SQL Query Error :");
			RedirectToMaintainPage();
		}

		if ($format=="list")
		{
			while($row=mysql_fetch_assoc($result))
			{
				$data[] = $row;
			}
		}
		else
		{
			$data=mysql_fetch_assoc($result);
		}
		mysql_close($link);
		return $data;
	}

	function GetDateTimeNow()
	{
		$sql = "SELECT DATE_FORMAT(NOW(), '%Y-%m-%d %h:%i:%s') AS DATETIME_NOW";
		$data = ExecQuery($sql, "one");
		return $data["DATETIME_NOW"];
	}

	function GetDateNow()
	{
		$sql = "SELECT DATE_FORMAT(NOW(), '%Y-%m-%d') AS DATE_NOW";
		$data = ExecQuery($sql, "one");
		return $data["DATE_NOW"];
	}

	function WriteErrorLog($ErrorMessage, $title="")
	{
		$realpath = realpath(dirname(__FILE__));
		$date = date('d-M-y');
		$logFile = $realpath . "/../logs/ErrorLogs_$date.log";
		$file = @fopen($logFile, 'a');
		if ($file == false)
			return false;
		$datenow = date('d-M-y H:i:s');
		$ErrorMessage = "\n\r\n\r [ " . $datenow . " ] ".$title." ". $ErrorMessage;
		fwrite($file, $ErrorMessage);
		fclose($file);
	}

?>
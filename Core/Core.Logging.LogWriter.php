<?php //namespace Code\Logging;
require_once("Core.Configuration.php");

//use Core\Configuration as Configuration;

class LogWriter
{
	public static function Instance()
	{
		return new LogWriter();	
	}
	
	public function WriteLog($fileName, $errorMessage)
	{
		$file = fopen($logFile, 'a');

		/*$datetime=new DateTime(); */
		//	fwrite($file, "Logging date:".$datetime->format('YmdHis'));
		/*fwrite($file, "\r\n\r\n------ Error Message ---------- \r\n");*/
		//fwrite($file, "-------------------------------------------\r\n\r\n");

		fwrite($file, $errorMessage);
		fclose($file);
	}

	public function WriteErrorLog($errorMessage)
	{
		date_default_timezone_set("Asia/Bangkok");
		$date = date('Y-m-d');
		$logFile = realpath(dirname(__FILE__)) . "/../_logs/ErrorLogs_$date.log";
		$file = @fopen($logFile, 'a');
		if ($file == false)
			return false;
		$errorMessage = "\n\r\n\r[" .date('Y-m-d H:i:s')."] ". $errorMessage;
		fwrite($file, $errorMessage);
		fclose($file);
	}

	public static function WriteTraceLogs($errorMessage)
	{
		date_default_timezone_set("Asia/Bangkok");
		$date = date('Y-m-d');
		$logFile = realpath(dirname(__FILE__)) . "/../_logs/TraceLogs_$date.log";
		$file = @fopen($logFile, 'a');
		if ($file == false)
			return false;
		$errorMessage = "\n\r\n\r[" .date('Y-m-d H:i:s')."] ". $errorMessage;
		fwrite($file, $errorMessage);
		fclose($file);
	}
	
}


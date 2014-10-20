<?php  //namespace Core\Database;
require_once(realpath(dirname(__FILE__))."/Core.Configuration.php");
require_once(realpath(dirname(__FILE__))."/Core.String.php");
require_once(realpath(dirname(__FILE__))."/Core.Logging.LogWriter.php");

class MySQL
{
	function __construct() 
	{
		Configuration::Initializing();	
	}

	public static function Instance()
	{
		return new MySQL();	
	}

	private function GetMySQLConnection()
	{
		$DB_HOST			= Configuration::$DB_HOST;
		$DB_USER				= Configuration::$DB_USER;
		$DB_PASSWORD	= Configuration::$DB_PASSWORD;
		$DB_NAME			= Configuration::$DB_NAME;

		$connection = @mysql_connect($DB_HOST, $DB_USER, $DB_PASSWORD);
		if ($connection == false)
			$this->ShowErrorMessage("MySQL Connection: ".mysql_error());

		$select_db = @mysql_select_db($DB_NAME, $connection);
		if ($select_db == false)
			$this->ShowErrorMessage("MySQL Select Database: ".mysql_error());
		
		$name_query = @mysql_query("SET NAMES UTF8");
		if ($name_query == false)
			$this->ShowErrorMessage("MySQL Query: ".mysql_error());

		date_default_timezone_set(Configuration::$TIMEZONE);
		return $connection;
	}
	
	//$store_procedure is a set of string of storeprocedure to execute
	//return type is a number of ID (it's an auto increment id are inserted.)
	public function ExecNonQuery($sql)
	{
		$conn = $this->GetMySQLConnection();

		if(is_array($sql))
		{
			$result=null;
			foreach($sql as $key=>$value)
			{
				$query_result = @mysql_query($value, $conn);
				if ($query_result == false)
					$this->ShowErrorMessage("MySQL Query: ".mysql_error()." [".$value."]");

				$result[] = MySQL::GetExecuteNonQueryResult($value);
			}
		}
		else
		{
			$query_result = @mysql_query($sql, $conn);
			if ($query_result == false)
				$this->ShowErrorMessage("MySQL Query: ".mysql_error()." [".$sql."]");

			$result = MySQL::GetExecuteNonQueryResult($sql);
		} 
		
		return $result;
	}

	private static function GetExecuteNonQueryResult($sql)
	{
		$result = 0;
		if(String::StartWith(strtoupper($sql), "INSERT"))
			$result = mysql_insert_id();
		else if(String::StartWith(strtoupper($sql), "UPDATE") || String::StartWith(strtoupper($sql), "DELETE"))
			$result = mysql_affected_rows();
		else
		{
			$result = mysql_insert_id();
			if(!isset($result))
			{
				$result = mysql_affected_rows();	
			}
		}		
		return $result;	
	}

	public function ExecScalar($sql)
	{
		$conn = $this->GetMySQLConnection();

		$result = @mysql_query($sql, $conn);
		if ($result == false)
			$this->ShowErrorMessage("MySQL Query: ".mysql_error()." [".$sql."]");

		$data = mysql_fetch_assoc($result);
		$value = NULL;
		if ($data != 0)
		{
			if (!empty($data))
			{
				$value = $data[Key($data)];
			}
		}
		mysql_close($conn);
		return $value;	
	}

	public function ExecQuery($sql, $get_direct_mysql_result=false)
	{
		$conn = $this->GetMySQLConnection();

		$result = @mysql_query($sql, $conn);
		if ($result == false)
			$this->ShowErrorMessage("MySQL Query: ".mysql_error()." [".$sql."]");
		
		if($get_direct_mysql_result)
		{
			mysql_close($conn);
			return $result;	
		}
		else
		{
			
			$data = null;
			if (!empty($result))
			{
				while($row=mysql_fetch_assoc($result))
				{
					$data[] = $row;
				}
			}

			mysql_close($conn);
			return $data;
		}
	}
	
	
	 
	
	
	public function ExecSPQuery($store_procedure)
	{
		return $this->ExecQuery("call ".$store_procedure);
	}

	public function ExecSPScalar($store_procedure)
	{
		return $this->ExecScalar("call ".$store_procedure);
	}

	public function ExecSPNonQuery($store_procedure)
	{
		return $this->ExecNonQuery("call ".$store_procedure);
	}
 
	public function GetCurrentDate()
	{
		return $this->ExecScalar("SELECT DATE_FORMAT(Now(), '%Y-%m-%d %H:%i:%s') AS NOW_DATE");
	}

	public function ShowErrorMessage($errorMessage)
	{
		if (empty($errorMessage))
			return;
		$this->WriteLog($errorMessage);
		echo "server busy! please try again later.";
		Exit();
	}

	public function WriteLog($errorMessage)
	{
		$LogWriter = LogWriter::Instance();
		$LogWriter->WriteErrorLog($errorMessage);
	}

}
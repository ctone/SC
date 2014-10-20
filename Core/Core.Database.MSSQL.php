<?php  //namespace Core\Database;

require_once(realpath(dirname(__FILE__))."/Core.Configuration.php");
require_once(realpath(dirname(__FILE__))."/Core.String.php");

class MSSQL
{
	function __construct() 
	{
		Configuration::Initializing();	
	}

	public static function Instance()
	{
		return new MSSQL();	
	}

	public function ExecNonQuery($sql)
	{
		$DB_HOST			= Configuration::$DB_HOST;
		$DB_USER				= Configuration::$DB_USER;
		$DB_PASSWORD	= Configuration::$DB_PASSWORD;
		$DB_NAME			= Configuration::$DB_NAME;
		$conn					= @mssql_connect($DB_HOST,$DB_USER,$DB_PASSWORD);
		if ($conn == FALSE)
		{
			self::GetErrorText(1, $DB_HOST, $sql);
			Exit();
		}
		mssql_select_db($DB_NAME, $conn);
		if(is_array($sql))
		{
			$result=null;
			foreach($sql as $key=>$value)
			{
				$query =  $value." SELECT @@IDENTITY AS NewID;";
				$result[] = mssql_query($query, $conn) or die ('ERROR: '.$sql.' : '.mssql_get_last_message());
				
				$data = mssql_fetch_assoc($result);
				$value = NULL;
				if ($data != 0)
				{
					if (!empty($data))
					{
						$value = $data[Key($data)];
					}
				}
				mssql_close($conn);
				return $value;	
			}
		}
		else
		{
			$query = $sql." SELECT @@IDENTITY AS NewID;";
			$result = mssql_query($query, $conn) or die ('ERROR: '.$sql.' : '.mssql_get_last_message());

			$data = mssql_fetch_assoc($result);
			$value = NULL;
			if ($data != 0)
			{
				if (!empty($data))
				{
					$value = $data[Key($data)];
				}
			}
			mssql_close($conn);
			return $value;	
		}
	}

	public function ExecScalar($sql)
	{
		$DB_HOST			=		Configuration::$DB_HOST;
		$DB_USER				=		Configuration::$DB_USER;
		$DB_PASSWORD	=		Configuration::$DB_PASSWORD;
		$DB_NAME			=		Configuration::$DB_NAME;
		$conn					=		@mssql_connect($DB_HOST,$DB_USER,$DB_PASSWORD);
		if ($conn == FALSE)
		{
			self::GetErrorText(1, $DB_HOST, $sql);
			Exit();
		}
		mssql_select_db($DB_NAME,$conn);
		$result = mssql_query($sql, $conn) or die ('ERROR: '.$sql.' : '.mssql_get_last_message());
		$data = mssql_fetch_assoc($result);
		$value = NULL;
		if ($data != 0)
		{
			if (!empty($data))
			{
				$value = $data[Key($data)];
			}
		}
		mssql_close($conn);
		return $value;	
	}

	public function ExecQuery($sql, $get_direct_mssql_result=false)
	{
		$DB_HOST				=		Configuration::$DB_HOST;
		$DB_USER					=		Configuration::$DB_USER;
		$DB_PASSWORD		=		Configuration::$DB_PASSWORD;
		$DB_NAME				=		Configuration::$DB_NAME;
		$conn						=		@mssql_connect($DB_HOST,$DB_USER,$DB_PASSWORD);
		if ($conn == FALSE)
		{
			self::GetErrorText(1, $DB_HOST, $sql);
			Exit();
		}
		mssql_select_db($DB_NAME, $conn);
		$result = mssql_query($sql, $conn) or die ('ERROR: '.$sql.' : '.mssql_get_last_message());
		if ($get_direct_mssql_result)
		{
			mssql_close($conn);
			return $result;	
		}
		else
		{
			$data = null;
			if (!empty($result))
			{
				while($row=mssql_fetch_assoc($result))
				{
					$data[] = $row;
				}
			}
			mssql_close($conn);
			return $data;
		}
	}
 
	public function GetCurrentDate()
	{
		return $this->ExecScalar("SELECT getDate() AS NOW_DATE");
	}

	private static function GetErrorText($type, $host="", $sql="")
	{
		if ($type == 1)
			echo "Could not connect to Server " .$host. " [ ". $sql . " ] ";
		else
			echo "Error Query : ". $sql;
	}

}
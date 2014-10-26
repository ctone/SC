<?php
require_once(realpath(dirname(__FILE__))."/../Core/Core.Database.MySQL.php");
require_once(realpath(dirname(__FILE__))."/../Core/Core.IO.FileUploader.php");
require_once(realpath(dirname(__FILE__))."/../Core/Core.Http.php");
class UserB
{
	/*public function __construct()
	{}*/
	public static function Instance()
	{
		return new UserB();
	}
		
	public function GetList()
	{
		$MySQL = MySQL::Instance();
		$sql = "SELECT * FROM user WHERE Status = 1";
		
		return $MySQL->ExecQuery($sql);  
	}
	
	public function GetByID(int $id)
	{
		$MySQL = MySQL::Instance();
		$sql = "SELECT * FROM user WHERE ID = $id";
		
		return $MySQL->ExecQuery($sql);  
	}
	
	public function GetByUsername(string $username)
	{
		$MySQL = MySQL::Instance();
		$sql = "SELECT * FROM user WHERE UserName =$username";
		
		return $MySQL->ExecQuery($sql);  
	}

	public function Create(string $username, string $pass)
	{
		$MySQL = MySQL::Instance();
		$sql = "INSERT INTO user 
				(`ID`, `UserName`, `Password`, `UpdateDate`, `Status`) 
				VALUES
				(NULL, '$username', '$pass', 'NOW()', '1')";
		
		$data = $MySQL->ExecNonQuery($sql);
		return $data;
	}
	
	public function UpdatePassword(int $id, string $pass)
	{
		$MySQL = MySQL::Instance();
		$sql = "UPDATE user SET `Password` = '$pass' WHERE ID = $id";
		$data = $MySQL->ExecNonQuery($sql);
		return $data;
	}

}
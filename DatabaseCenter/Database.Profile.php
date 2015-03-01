<?php
require_once(realpath(dirname(__FILE__))."/../Core/Core.Database.MySQL.php");
require_once(realpath(dirname(__FILE__))."/../Core/Core.IO.FileUploader.php");
require_once(realpath(dirname(__FILE__))."/../Core/Core.Http.php");
class ProfileDB
{
	/*public function __construct()
	{}*/
	public static function Instance()
	{
		return new ProfileDB();
	}
		
	public function Get()
	{	
		$MySQL = MySQL::Instance();
		$sql = "SELECT * FROM profile WHERE ID = 1";
		
		return $MySQL->ExecQuery($sql);  
	}
	
	public function UpdatePassword(string $message)
	{
		$MySQL = MySQL::Instance();
		$sql = "UPDATE profile SET `Messege` = '$pass' WHERE ID = 1";
		$data = $MySQL->ExecNonQuery($sql);
		return $data;
	}

}
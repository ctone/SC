<?php
require_once(realpath(dirname(__FILE__))."/../Core/Core.Database.MySQL.php");
require_once(realpath(dirname(__FILE__))."/../Core/Core.IO.FileUploader.php");
require_once(realpath(dirname(__FILE__))."/../Core/Core.Http.php");
class ContactInfoDB
{
	/*public function __construct()
	{}*/
	public static function Instance()
	{
		return new ContactInfoDB();
	}
	
	public function Get()
	{	
		$MySQL = MySQL::Instance();
		$sql = "SELECT * FROM contactinfo WHERE ID = 1";
		
		return $MySQL->ExecQuery($sql);  
	}
	

	public function Update(string $name, string $email, string $phone, string $home, string $fax, string $address)
	{
		$MySQL = MySQL::Instance();
		$sql = "UPDATE contactinfo 
				SET `Name` = '$name'
				`Email` = '$email',
				`Phone` = '$phone',
				`Home` = '$home',
				`Fax` = '$fax',
				`Address` = '$address'				
				WHERE ID = 1";
		$data = $MySQL->ExecNonQuery($sql);
		return $data;
	}

}
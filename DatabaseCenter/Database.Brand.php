<?php
require_once(realpath(dirname(__FILE__))."/../Core/Core.Database.MySQL.php");
require_once(realpath(dirname(__FILE__))."/../Core/Core.IO.FileUploader.php");
require_once(realpath(dirname(__FILE__))."/../Core/Core.Http.php");
class BrandDB
{
	/*public function __construct()
	{}*/
	public static function Instance()
	{
		return new BrandDB();
	}
		
	public function GetList()
	{
		$MySQL = MySQL::Instance();
		$sql = "SELECT * FROM brand WHERE Status = 1";
		
		return $MySQL->ExecQuery($sql);  
	}
	
	public function GetByID(int $id)
	{
		$MySQL = MySQL::Instance();
		$sql = "SELECT * FROM brand WHERE ID = $id";
		
		return $MySQL->ExecQuery($sql);  
	}
	


	public function Create(string $name, string $description, string $thumbnail)
	{
		$MySQL = MySQL::Instance();
		$sql = "INSERT INTO brand 
				(`Name`, `Description`, `Thumbnail`, `UpdateBy`, `UpdateDate`, `Status`)
				VALUES
				('$name', '$description', '$thumbnail', 1, 'NOW()', 1)";
		
		$data = $MySQL->ExecNonQuery($sql);
		return $data;
	}
	
	public function Update(int $id, string $name, string $description, string $thumbnail)
		$MySQL = MySQL::Instance();
		$sql = "UPDATE brand 
				SET 
				`Name` = '$name',
				`Description` = '$description',
				`Thumbnail` = '$thumbnail'				
				WHERE ID = $id";
		$data = $MySQL->ExecNonQuery($sql);
		return $data;
	}

}
<?php
require_once(realpath(dirname(__FILE__))."/../Core/Core.Database.MySQL.php");
require_once(realpath(dirname(__FILE__))."/../Core/Core.IO.FileUploader.php");
require_once(realpath(dirname(__FILE__))."/../Core/Core.Http.php");
class DocumentDB
{
	/*public function __construct()
	{}*/
	public static function Instance()
	{
		return new DocumentDB();
	}
		
	public function GetList()
	{
		$MySQL = MySQL::Instance();
		$sql = "SELECT * FROM document WHERE Status = 1";
		
		return $MySQL->ExecQuery($sql);  
	}
	
	public function GetByID(int $id)
	{
		$MySQL = MySQL::Instance();
		$sql = "SELECT * FROM document WHERE ID = $id";
		
		return $MySQL->ExecQuery($sql);  
	}
	
	public function Create(string $name, string $filename, int $brandId)
	{
		$MySQL = MySQL::Instance();
		$sql = "INSERT INTO document 
				(`ID`, `Name`, `FileName`, `BrandID`, `UpdateBy`, `UpdateDate`, `Status`) 
				VALUES
				(NULL, '$name', '$filename', $description, 1, 'NOW()', '1')";
		
		$data = $MySQL->ExecNonQuery($sql);
		return $data;
	}
	
	public function Update(int $id, string $name, string $filename)
	{
		$MySQL = MySQL::Instance();
		$sql = "UPDATE document 
				`Name` = '$name',
				`FileName` = '$filename'
				WHERE ID = $id";
		$data = $MySQL->ExecNonQuery($sql);
		return $data;
	}
}
<?php
require_once ("appconfig.php");
dbconfig::prePareDatabaseConfig();
class dbconfig
{
	public static $DB_HOST;
	public static $DB_USER;
	public static $DB_PASSWORD;
	public static $DB_NAME;
	public static function prePareDatabaseConfig()
	{
		$IS_DEV = AppConfig::GetCurrentDeployStatus();
		if ($IS_DEV == 1)
		{
			dbconfig::$DB_HOST				= "localhost";
			dbconfig::$DB_USER				= "root";
			dbconfig::$DB_PASSWORD			= "1234";
			dbconfig::$DB_NAME				= "sc";
		}
		else												// Production
		{
			dbconfig::$DB_HOST				= "localhost";
			dbconfig::$DB_USER				= "usr_bridgesawu";
			dbconfig::$DB_PASSWORD		= "7RoVvbrr9Yw";
			dbconfig::$DB_NAME				= "fb_bridgesawu";
		}
	}
}
?>
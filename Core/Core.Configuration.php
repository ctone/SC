<?PHP //namespace Core;

require_once(realpath(dirname(__FILE__))."/Core.Configuration.ConfigurationManager.php");

class Configuration
{

	public static $DB_HOST;
	public static $DB_USER;
	public static $DB_PASSWORD;
	public static $DB_NAME;
	public static $XMLDOC;
	public static $TIMEZONE;
	
	public static $YOUTUBE_USER;
	public static $YOUTUBE_PASSWORD;
	public static $YOUTUBE_DEVELOPER_KEY;
	public static $YOUTUBE_APP_ID;
	public static $YOUTUBE_AUTHENTICATION_URL;
	public static $YOUTUBE_VIDEO_CATEGORY;
	public static $YOUTUBE_UPLOAD_URL;
	
	public static function Initializing()
	{
		$conf = new ConfigurationManager();
		Configuration::$DB_HOST				=  $conf->GetDefaultDatabaseConfig("Server");
		Configuration::$DB_USER				=  $conf->GetDefaultDatabaseConfig("User");
		Configuration::$DB_PASSWORD			=  $conf->GetDefaultDatabaseConfig("Password");
		Configuration::$DB_NAME				=  $conf->GetDefaultDatabaseConfig("Database");
		Configuration::$TIMEZONE			=  $conf->GetDefaultDatabaseConfig("TimeZone");
	}

	public static function GetConfig($key)
	{
		$conf = new ConfigurationManager();
		return  $conf->GetAttribute($key);
	}
}
 
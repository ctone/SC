<?php
 
require_once(realpath(dirname(__FILE__))."/Core.String.php");

class ConfigurationManager
{
	private static $XMLDOC;
	private static $CONFIG_NAME;
 	
	function __construct($conf_name="") 
	{
		if(empty($conf_name))
		{
			$conf_name = realpath(dirname(__FILE__))."/../app.conf";
		}
		ConfigurationManager::$XMLDOC = $this->LoadXMLConfiguration($conf_name);
	 
	}
	
	private static function LoadXMLConfiguration($conf_name)
	{
		$xmlDoc = simplexml_load_file($conf_name);
		return $xmlDoc;	
	}

	public function GetValue($key)
	{
	}
	
	public function GetAttribute($key)
	{
		$nodes	=	ConfigurationManager::$XMLDOC->xpath(sprintf('/appSetting/Application/add[@name="%s"]', $key)); 
		if(count($nodes) ==1)
		{
			$json = json_encode($nodes[0]->attributes());
			//$config = json_decode($json);
			$config = json_decode($json,true);
			return $config["@attributes"]["value"];
		}
		else
		{
			die(String::Format("Error: Configuration '{0}' was not found.", $key));
		}
	}
	
	public  function GetDatabaseConfig($id, $key)
	{}
 
	public  function GetDefaultDatabaseConfig($key)
	{
		$nodes =	ConfigurationManager::$XMLDOC->xpath(sprintf('/appSetting/Databases/Database[@Active="%s"]', 1)); 
		if(count($nodes) ==1)
		{
			$json = json_encode($nodes[0]);
			//$config = json_decode($json);
			$config = json_decode($json,true);
			return $config[$key];
		}
		else
		{
			die("Error: Invalid database configuration.");
		}
	}	
	
}

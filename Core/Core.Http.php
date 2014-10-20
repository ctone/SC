<?php
require_once(realpath(dirname(__FILE__))."/Core.String.php");
class Http  {

	function Http() {

	}
	
	//$safeScript =		remove				<removing only unsafe tag, keep the remaining data>
	//					convert				<Html encrypt the unsafe tag, to extract use "unhtmlentities()">
	//					reject (default)	<return empty string when unsafe tag detected>
	public static function HttpPost($key, $safeScript="reject")
	{
		if (!Http::IsHttpPostExisted($key))
			return null;
		
		$value	= $_POST[$key];
				
		if(is_array($value))
		{
			$arrayvalue = array();
			foreach($value as $k=>$v)
			{
				$arrayvalue[] = Http::ConvertData($v, $safeScript);
			}
			return $arrayvalue;
		}
		else
		{
			$value = Http::ConvertData($value, $safeScript);
			return $value;
		}
		
	}
	
	public static function HttpGet($key, $safeScript="reject")
	{
		if (!Http::IsHttpGetExisted($key))
			return null;
	
		$value	= $_GET[$key];

		return Http::ConvertData($value, $safeScript);
	}
	
	private static function ConvertData($data, $safeScript)
	{		
		if(String::DetectXSS($data))
		{
			switch (strtolower($safeScript))
			{
				case "remove" :
					return strip_tags($data);
					break;
				case "convert" :
					return htmlentities($data);
					break;
				default:
					return "";					
			}
		}
		return $data;
	}
	
	public static function IsHttpPostExisted($key)
	{
		return array_key_exists($key, $_POST);	
	}

	public static function IsHttpGetExisted($key)
	{
		return array_key_exists($key, $_GET);	
	}

	public static function GetIP()
	{ 
		$ip = '';
		if (isset($_SERVER["REMOTE_ADDR"])) 
			$ip = $_SERVER["REMOTE_ADDR"]; 
		else if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) 
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"]; 
		else if (isset($_SERVER["HTTP_CLIENT_IP"])) 
			$ip = $_SERVER["HTTP_CLIENT_IP"]; 
		return $ip; 
	}
	
	
}
 
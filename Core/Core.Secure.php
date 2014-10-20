<?php

class Secure
{
	private static $encryptKey = "@g3r3zaq12wsxcde34rfvbgt56yhnmju78ik90";

	public static function Encrypt($arrayPlainText, $key="@g3r3zaq12wsxcde34rfvbgt56yhnmju78ik90")
	{
		if (count($arrayPlainText) == 0)
			return "";
		
		if (!is_array($arrayPlainText))
			$arrayPlainText = array ($arrayPlainText);
		//return self::safe_b64encode($arrayPlainText);

		$strPlainText = serialize($arrayPlainText);

		if (function_exists("mcrypt_encrypt"))
		{
			$encrypted = Secure::safe_b64encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, 
				md5($key), 
				$strPlainText, 
				MCRYPT_MODE_CBC, 
				md5(md5($key))));
		}
		else
		{
			$encrypted = Secure::safe_b64encode($strPlainText);
		}

		return $encrypted;
	}
	public static function Decrypt($CipherText, $key="@g3r3zaq12wsxcde34rfvbgt56yhnmju78ik90")
	{
		if (function_exists("mcrypt_encrypt"))
		{
			$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, 
				md5($key), 
				Secure::safe_b64decode($CipherText), 
				MCRYPT_MODE_CBC, 
				md5(md5($key))), "\0");
		}
		else
		{
			$decrypted = Secure::safe_b64decode($CipherText);
		}

		return unserialize($decrypted);
	}	

	public static function CollapesArray($array)
	{
		$strBase = "";
		foreach ($array as $key => $value) 
		{
			if ($key == 0)
				$strBase .= $value;
			else
				$strBase .= "^%&^%&" . $value;
		}
		return $strBase;
	}
	public static function ExplodeArray($array)
	{
		return explode("^%&^%&", $array);
	}

	public static function safe_b64encode($string) 
	{
		$data = base64_encode($string);
		$data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
		return $data;
	}
	public static function safe_b64decode($string) 
	{
		$data = str_replace(array('-', '_'), array('+', '/'), $string);
		$mod4 = strlen($data) % 4;
		if ($mod4) 
		{
			$data .= substr('====', $mod4);
		}
		return base64_decode($data);
	}

	//FOR ASIAN HONDA PROJECT USE ONLY
	public static function EncryptMenuData($categoryId, $contentId, $mediaType, $type)
	{		
		$params = array();
				
		if (!empty($categoryId))
			$params["c"] = $categoryId;
		if (!empty($contentId))
			$params["ct"] = $contentId;
		if (!empty($mediaType))
			$params["md"] = $mediaType;
		if (!empty($type))
			$params["nt"] = $type;
		
		return Secure::Encrypt($params);
	}
	public static function EncryptData($page, $limit, $categoryId, $contentId, $type)
	{
		if (empty($page))
			$page = 1;
		if (empty($limit))
			$limit = 10;
		
		$params = array();
		
		$params["p"] = $page;
		$params["lm"] = $limit;
		
		if (!empty($categoryId))
			$params["c"] = $categoryId;
		if (!empty($contentId))
			$params["ct"] = $contentId;
		if (!empty($type))
			$params["nt"] = $type;
		
		return Secure::Encrypt($params);
	}
	public static function EncryptActivitySearch($page, $limit, $dayFrom, $monthFrom, $yearFrom, $dayTo, $monthTo, $yearTo)
	{
		if (empty($page))
			$page = 1;
		if (empty($limit))
			$limit = 10;
		
		$params = array("p"=>$page, "lm"=>$limit, "df"=>$dayFrom, "mf"=>$monthFrom, "yf"=>$yearFrom, "dt"=>$dayTo, "mt"=>$monthTo, "yt"=>$yearTo);
		return Secure::Encrypt($params);
	}
}
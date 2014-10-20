<?php
require_once(realpath(dirname(__FILE__))."/Core.Configuration.php");
require_once(realpath(dirname(__FILE__))."/Core.Http.php");

class Cryptography
{
	private static $encryptKey = "@g3r3zaq12wsxcde34rfvbgt56yhnmju78ik90";

	public static function Encrypt($arrayPlainText)
	{
		if (count($arrayPlainText) == 0)
			return "";
		
		if (!is_array($arrayPlainText))
			$arrayPlainText = array ($arrayPlainText);

		$strPlainText = self::CollapesArray($arrayPlainText);

		if (function_exists("mcrypt_encrypt"))
		{
			$encrypted = self::safe_b64encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5(self::$encryptKey), $strPlainText, MCRYPT_MODE_CBC, md5(md5(self::$encryptKey))));
		}
		else
		{
			//TODO ::
			$encrypted = self::safe_b64encode($strPlainText);
		}

		return $encrypted;
	}

	public static function Decrypt($CipherText)
	{
		if (function_exists("mcrypt_encrypt"))
		{
			$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5(self::$encryptKey), self::safe_b64decode($CipherText), MCRYPT_MODE_CBC, md5(md5(self::$encryptKey))), "\0");
		}
		else
		{
			//TODO ::
			$decrypted = self::safe_b64decode($CipherText);
		}

		return self::ExplodeArray($decrypted);
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






}//end class

<?php

class Validator
{
	// 0-9 . 1e4	
	public static function IsNumber($number)
	{
		if (is_numeric($number))
			return true;
		else
			return false;
	}

	public static function IsInt($number)
	{
		if (is_int($number))
			return true;
		else
			return false;
	}

	// ex. 23.5, 2e3
	public static function IsDecimalNumber($number)
	{
		if (is_float($number))
			return true;
		else
			return false;
	}

	public static function IsArray($array)
	{
		if (is_array($array))
			return true;
		else
			return false;
	}

	public static function IsString($text)
	{
		if (is_string($text))
			return true;
		else
			return false;
	}
	
	public static function IsDate($date)
	{
		
		$date = str_replace(array('\'', '-', '.', ','), '/', $date); 
		$date = explode('/', $date);
		
		if (count($date) == 1 AND is_numeric($date[0]) AND $date[0] < 20991231 AND (checkdate(substr($date[0], 4, 2) , substr($date[0], 6, 2) , substr($date[0], 0, 4))))
		{
			return true;
		}

		if (count($date) == 3 AND is_numeric($date[0]) AND is_numeric($date[1]) AND is_numeric($date[2]) AND (checkdate($date[0], $date[1], $date[2]) OR checkdate($date[1], $date[0], $date[2]) OR checkdate($date[1], $date[2], $date[0])))
		{ 
			return true;
		}

		return false;
	}

	
	public static function IsValidEmail($email)
	{
		//-----Old version---
		/*$pattern = "^[a-zA-Z][a-zA-Z0-9._\-]+@[a-zA-Z0-9._\-]+.([a-zA-Z]{2,4})$";
		if (eregi($pattern, $email))
			return true;
		else
			return false;*/
			
		$pattern = "/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i";
		
		if (preg_match($pattern, $email)) {
			return true;
		} else {
			return false;
		}	
		
	}
	
	//IsValidMobile 10 digits start with 08, 09// 11 digits start with 668, 669
	public static function IsValidMobile($number)
	{
		$pattern = "^(08|09)";
		$pattern2 = "^(668|669)";
		if (!is_numeric($number))
			return false;
		if (strlen($number)==10 && eregi($pattern, $number))
			return true;
		if (strlen($number)==11 && eregi($pattern2, $number))
			return true;
		return false;
	}

	//IsValidPhone 9 digits start with 0 / 10 digits start with 66
	public static function IsValidPhone($number)
	{
		$pattern = "^0";
		$pattern2 = "^(66)";
		if (!is_numeric($number))
			return false;
		if (strlen($number)==9 && eregi($pattern, $number))
			return true;
		if (strlen($number)==10 && eregi($pattern2, $number))
			return true;
		return false;
	}
	
	//IsAlphabatic << no number, no symbol, no special character, no white space
	public static function IsAlphabatic($text)
	{
		$pattern = "^[a-zA-Z]*$";
		if (eregi($pattern, $text))
			return true;
		else
			return false;
	}


}
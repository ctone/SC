<?php

class DateTimeConvertor  
{
/*
	echo DateTimeConvertor::ConvertorYMD2DMY("2011/12/31","-") ."<br>" ;
	echo DateTimeConvertor::ConvertorYMD2DMMY("2011/12/31","-") ."<br>" ;
	echo DateTimeConvertor::ConvertorYMD2DMMMY("2011/12/31","-") ."<br>" ;
*/

	public $_Date;
	
	function DateTimeConvertor($Date)
	{
		$this->_Date = $Date;
	}
	
	function GetDay()
	{
		$arr = explode('/',$this->_Date);
		return $arr[2];
	}
	
	function GetMonth()
	{
		$arr = explode('/',$this->_Date);
		return $arr[1];
	}
	
	function GetYear()
	{
		$arr = explode('/',$this->_Date);
		return $arr[0];
	}
	
	static function ConvertorYMD2DMY($Date,$DisplaySeparate)
	{
		$arrDate = explode('/',$Date);
		if(count($arrDate) == 3)
		{
			return $arrDate[2] .$DisplaySeparate. $arrDate[1] .$DisplaySeparate. $arrDate[0];
		}
		else
			return "";
	}
	
	static function ConvertorYMD2DMMY($Date,$DisplaySeparate)
	{
		$arrDate = explode('/',$Date);
		$month = "";
		
		if(count($arrDate) == 3)
		{
			switch($arrDate[1])
			{
				case "1":
					$month = "Jan";
					break;
				case "2":
					$month = "Feb";
					break;
				case "3":
					$month = "Mar";
					break;
				case "4":
					$month = "Apl";
					break;
				case "5":
					$month = "May";
					break;
				case "6":
					$month = "Jun";
					break;
				case "7":
					$month = "Jul";
					break;
				case "8":
					$month = "Aug";
					break;
				case "9":
					$month = "Sep";
					break;
				case "10":
					$month = "Oct";
					break;
				case "11":
					$month = "Nov";
					break;
				case "12":
					$month = "Dec";
					break;

			}
			return $arrDate[2] .$DisplaySeparate. $month .$DisplaySeparate. $arrDate[0];
		}
		else
			return "";
	}
	
	static function ConvertorYMD2DMMMY($Date,$DisplaySeparate)
	{
		$arrDate = explode('/',$Date);
		$month = "";
		
		if(count($arrDate) == 3)
		{
			switch($arrDate[1])
			{
				case "1":
					$month = "January";
					break;
				case "2":
					$month = "February";
					break;
				case "3":
					$month = "March";
					break;
				case "4":
					$month = "April";
					break;
				case "5":
					$month = "May";
					break;
				case "6":
					$month = "Jun";
					break;
				case "7":
					$month = "July";
					break;
				case "8":
					$month = "August";
					break;
				case "9":
					$month = "September";
					break;
				case "10":
					$month = "October";
					break;
				case "11":
					$month = "November";
					break;
				case "12":
					$month = "December";
					break;

			}
			return $arrDate[2] .$DisplaySeparate. $month .$DisplaySeparate. $arrDate[0];
		}
		else
			return "";
	}
}

 
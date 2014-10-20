<?php

class DataConverter
{
	public static function DateToString($date, $format="")
	{
		if (empty($format))
			$format = "Y-m-d H:i:s";
		return date($format , strtotime($date));
	}
	// default format = yyyy-mm-dd HH:mi:ss Ex. 2011-01-31 07:11:00

	public static function StringToDate($date, $format="")
	{
		
	}

	public static function GetThaiDateFormat($date)
	{
		// TODO :: check date before use this function 
		//$date= strtotime($date);
		$year =date("Y", $date);
		$addyears = 0;
		if($year < 2500)
			$addyears = 543;
		
		$thai_year=date("Y", $date)+$addyears;
		$month= date("m", $date);
		$day =  date("d", $date);
		$h=  date("H", $date);
		$i=  date("i", $date);
		$s=  date("s", $date);
		$thai_date = $day."/".$month."/".$thai_year;
 
		return $thai_date;
	}


}

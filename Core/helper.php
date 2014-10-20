<?php
	
require_once("dbhelper.php");

class Helper
{
	public static function ParentRedirect($url)
	{
		echo '<script>window.parent.location = \''.$url.'\'</script>';
	}

	public static function GetIP()
	{
		$ip ='';
		if (isset($_SERVER["REMOTE_ADDR"])) 
			$ip = $_SERVER["REMOTE_ADDR"]; 
		else if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) 
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"]; 
		else if (isset($_SERVER["HTTP_CLIENT_IP"])) 
			$ip = $_SERVER["HTTP_CLIENT_IP"]; 
		return $ip; 
	}

	public static function GetGUID()
	{
		if (function_exists('com_create_guid'))
		{
			return com_create_guid();
		}
		else
		{
			mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
			$charid = strtoupper(md5(uniqid(rand(), true)));
			$hyphen = chr(45);// "-"
			$uuid = chr(123)// "{"
			.substr($charid, 0, 8).$hyphen
			.substr($charid, 8, 4).$hyphen
			.substr($charid,12, 4).$hyphen
			.substr($charid,16, 4).$hyphen
			.substr($charid,20,12)
			.chr(125);// "}"
			return $uuid;
		}
	}

	public static function DateDiff($start, $end, $return="allsecs")
	{
		// end - start
		$uts['start']      =    strtotime($start);
		$uts['end']       =    strtotime($end);
		if( $uts['start']!==-1 && $uts['end']!==-1 )
		{
			if( $uts['end'] >= $uts['start'] )
			{
				$flag = false;
				$diff = $uts['end'] - $uts['start'];
			}
			else
			{
				$flag = true;
				$diff = $uts['start'] - $uts['end'];
			}
			if ($days=intval((floor($diff/86400))))
				$diff = $diff % 86400;
			if ($hours=intval((floor($diff/3600))))
				$diff = $diff % 3600;
			if ($minutes=intval((floor($diff/60))))
				$diff = $diff % 60;
			$diff = intval($diff);
			$allsecs = (($days*24*60*60)+($hours*60*60)+($minutes*60)+($diff));
			if ($flag == true)
			{
				$allsecs = 0 - $allsecs;
				$days = 0 - $days;
				$hours = 0 - $hours;
				$minutes = 0 - $minutes;
				$diff = 0 - $diff;
			}
			if ($return == "array")
				return (array('days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff, 'allseconds'=>$allsecs));
			else
				return $allsecs;
		}
		return false;
	}
	
	public static function Encode($params)
	{
		$ips = explode(".", Helper::GetIP(), 4);
		$strBase = $ips[3] . "#@";

		foreach ($params as $key => $value) {
			$strBase = $strBase . $ips[2] . "^%&" . $value . "^%&";
		}
		$strBase = $strBase . "#@" . $ips[1] . "#@" . $ips[0];
		
		return base64_encode($strBase);
	}
	
	public static function Decode($strBase)
	{
		$ips		= explode(".", Helper::GetIP(), 4);
		$decData	= base64_decode($strBase);
		$data		= explode("#@", $decData);
		$cnt		= count($data);
		if ($cnt < 4)
			return "COUNT:".$cnt.", RAW:".$strBase.", DEC:".$decData.", SPLIT:".$data;
		if ($data[0] != $ips[3])
			return $data[0]."|".$ips[3];
		if ($data[$cnt-1] != $ips[0])
			return $data[$cnt-1]."|".$ips[0];
		if ($data[$cnt-2] != $ips[1])
			return $data[$cnt-2]."|".$ips[1];
		
		$item = array();
		
		foreach ($data as $key => $value) {
			
			$element = explode("^%&", $value);
			if (count($element) < 2)
				continue;
			
			for ($i = 1; $i < count($element); $i+=2)
			{
				$item[] = $element[$i];
			}
		}	
		return $item;
	}
	
	public static function SuspeciousActionLog($customer_id, $fb_id, $ipAddress, $referrer, $url, $page, $code, $message, $description)
	{
		
		if (empty($ipAddress))
			$ipAddress = Helper::GetIP();
		
		if (empty($customer_id))
			$customer_id = "NULL";
		if (empty($fb_id))
			$fb_id = "NULL";
		
		$sql = "INSERT INTO suspecious_log (customer_id, fb_id, ip, referrer, url, page, code, message, description, create_date) 
			VALUES ($customer_id, $fb_id, '$ipAddress', '$referrer', '$url', '$page', '$code', '$message', '$description', NOW());";
			
		ExecNonQuery($sql);
	}
	
	public static function FormatUrl($url, $isExternalLink=false)
	{
		if (!$isExternalLink)
			return $url;
		
		if (strpos($url, "http:") === 0)
			return $url;
		return "http://".$url;
	}
}
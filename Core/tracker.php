<script src='js/tracker.js' type='text/javaScript' charset='utf-8'></script>

<?php
	
class tracker
{
	public static function DoParentRedirect($url, $targetPage, $currentPage, $clickFrom, $customerId, $fbId)
	{
		echo  "<script>". tracker::ParentRedirect($url, $targetPage, $currentPage, $clickFrom, $customerId, $fbId) . "</script>";
	}
	public static function GetParentRedirectScript($url, $targetPage, $currentPage, $clickFrom, $customerId, $fbId)
	{
		echo  "javascript::". tracker::ParentRedirect($url, $targetPage, $currentPage, $clickFrom, $customerId, $fbId);
	}
	public static function ParentRedirect($url, $targetPage, $currentPage, $clickFrom, $customerId, $fbId)
	{
		return "ParentRedirect('".$url."','".$targetPage."','".$currentPage."','".$clickFrom."',".$customerId.",".$fbId.");";
	}
	
	public static function GetEncodedOpenLink($url, $targetPage, $currentPage, $clickFrom, $customerId, $fbId)
	{
		echo  tracker::Open($url, rawurlencode($targetPage), $currentPage, $clickFrom, $customerId, $fbId, true);
	}

	public static function GetOpenLink($url, $targetPage, $currentPage, $clickFrom, $customerId, $fbId)
	{
		echo  tracker::Open($url, $targetPage, $currentPage, $clickFrom, $customerId, $fbId, false);
	}

	public static function Open($url, $targetPage, $currentPage, $clickFrom, $customerId, $fbId, $isEncode)
	{
		if ($isEncode)
			return "do.track.php?url=".$url."&ac=".$customerId."&af=".$fbId."&aa=1"."&ap=".$currentPage."&ae=".$clickFrom."&fly=y&ec=y"."&at=".$targetPage;
		else
			return "do.track.php?url=".$url."&ac=".$customerId."&af=".$fbId."&aa=1"."&ap=".$currentPage."&ae=".$clickFrom."&fly=y"."&at=".$targetPage;
	}
	
	public static function GetShareLink($url, $targetPage, $currentPage, $clickFrom, $customerId, $fbId)
	{
		echo tracker::ShareLink($url, $targetPage, $currentPage, $clickFrom, $customerId, $fbId);
	}

	public static function ShareLink($url, $targetPage, $currentPage, $clickFrom, $customerId, $fbId)
	{
		return "do.track.php?url=".$url."&ac=".$customerId."&af=".$fbId."&aa=1"."&ap=".$currentPage."&ae=".$clickFrom."&fly=share"."&at=".$targetPage;
	}
	
}
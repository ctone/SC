<?php

class AppConfig
{
	public static $GA_CODE_DEV = "UA-31194955-2";
	public static $GA_CODE_PRO = "UA-31194955-3";
	
	public static function GetCurrentDeployStatus()
	{
		// STATUS
		// 1 = DEVELOPE
		// 0 = DEPLOY
		return 1;
	}
	
	public static function GetCurrentMaintenanceStatus()
	{
		// 1 = NORMAL
		// 2 = MAINTENANCE
		return 1;
	}

}
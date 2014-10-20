<?php //namespace Core\HttpContext2;

require_once(realpath(dirname(__FILE__))."/Core.Configuration.php");
require_once(realpath(dirname(__FILE__))."/Core.String.php");
require_once(realpath(dirname(__FILE__))."/Core.Http.php");

class Page
{
	public static function Initializing($req_login=false, $req_permission=false)
	{
		$RequiredLogin = $req_login;
	
		if(!isset($RequiredLogin))
			$RequiredLogin=true;

		$conf=new Configuration();
		$redirect_url = $conf->GetConfig("LoginPage");

		if(!empty($RequiredLogin))
		{	
			$has_permission = false;
			$is_can_view = false;
			$is_login = Page::IsLogin();
			$has_permission= $is_login;
			
			if($req_permission)
			{
				Security::ValidatePermission();	
			}
		}
		else
		{
			//a caller page not required login
			$has_permission = true;	
		}	
		
	 	if(!$has_permission)
		{
			Page::Redirect($redirect_url);
			exit();	
		}		 
	}
	
	public static function LogOut()
	{
		SessionManager::ClearSessions();
		$conf = new Configuration();
		$login_page = $conf->GetConfig("MainPage");

		Page::Redirect($login_page);
	}
	
	public static function Login($usr, $pwd)
	{
		$user_biz = UsersBiz::Instance();
		$user_login = $user_biz->ValidateUserPassword($usr, $pwd);
	  
		if (empty($user_login) && count($user_login) <= 0)
			return false;
		else
		{
			//SessionManager::SetSession("CURRENT_LOGIN", $user_login[0]);
			$Login = new Security($user_login[0]);
			return true;
		}
	}
	

	//TODO: valid post back page
	public static function IsPostBack()
	{
		return (!empty($_POST) || count($_POST) > 0 || !empty($_FILES) || count($_FILES) > 0);
	}
	
	//TODO: not implemented yet!
	public static function GetCurrentLoginId()
	{
		$current_user = Page::GetCurrentLogin();
		if(!empty($current_user))
		{
			return $current_user["id"];
		}
		else
		{
			return 0;
		}
	}

	public static function GetCurrentLogin()
	{
		$CONFIG_SESSION_NAME = Configuration::GetConfig("SESSION_NAME");
		$session_name = "CURRENT_LOGIN_". $CONFIG_SESSION_NAME;

		$current_login = SessionManager::GetSession($session_name);
		return $current_login;
	}
	
	public static function IsLogin()
	{
		$CONFIG_SESSION_NAME = Configuration::GetConfig("SESSION_NAME");
		$session_name = "CURRENT_LOGIN_". $CONFIG_SESSION_NAME;
		$is_login = SessionManager::GetSession($session_name);
		return !empty($is_login);	
	}
	
	public static function Redirect($url)
	{
		//$url	=	urlencode($url);
		echo String::Format("<script language='JavaScript'> window.location = \"{0}\"; </script>", $url);	
	}	
	
	public static function MessageBox($msg)
	{
		$msg = str_replace("\"","'", $msg);
		echo String::Format("<script language='JavaScript'>alert(\"{0}\");</script>", $msg);	
	}

	public static function GetCurrentURIPath()
	{
		$SERVER_NAME = $_SERVER["HTTP_HOST"];
		$SERVER_PORT = $_SERVER["SERVER_PORT"];
		$REQUEST_URI = dirname($_SERVER['PHP_SELF']);
		$SERVER_PROTOCAL =  ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://' );
		return $SERVER_PROTOCAL . $SERVER_NAME . $REQUEST_URI;
	}

	public static function GetIPAddress()
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

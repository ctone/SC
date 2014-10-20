<?php

require_once (realpath(dirname(__FILE__))."/class.phpmailer.php");
require_once (realpath(dirname(__FILE__))."/Core.Configuration.php");
require_once (realpath(dirname(__FILE__))."/Core.String.php");
require_once (realpath(dirname(__FILE__))."/Core.Configuration.ConfigurationManager.php");

class SMTP
{
	public static function SendMail($from, $to, $cc="", $subject, $message)
	{
		$conf = new ConfigurationManager();
		$SMTPServer = $conf->GetAttribute("SMTPServer");
		$SMTPUsername = $conf->GetAttribute("SMTPUsername");
		$SMTPPassword = $conf->GetAttribute("SMTPPassword");


			
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->Host = $SMTPServer;
		$mail->Port = 25;
		$mail->SMTPAuth = true;
		$mail->Username = $SMTPUsername;
		$mail->Password = $SMTPPassword;
		$mail->SetFrom($from);
		$mail->AddAddress($to);
		$mail->Subject = $subject;
		$mail->Body = $message;
		if (!$mail->Send())
			return true;
		else
			return false;
	}
	 

	function SendMail2($SentMail)
	{
		$strTo = $SentMail;
		$strSubject = "Smirnoff Nightlife Exchange Project";
		$strHeader  = "MIME-Version: 1.0\r\n";
		$strHeader .= "Content-type: text/html; charset=utf-8\r\n";
		$strHeader .= "From: no-reply@beclearlyoriginal.com";
		$strMessage = "ยินดีด้วย!! คุณได้ตั๋วไปมันส์ในงาน Smirnoff Nightlife Exchange Project แล้วเจอกัน 12 พ.ย.นี้ BE THERE <a href='https://www.beclearlyoriginal.com/dublinexchange/register.php' >คลิกที่ลิ้งค์เพื่อลงทะเบียน</a>";

		$flgSend = @mail($strTo,$strSubject,$strMessage,$strHeader);  // @ = No Show Error //
		if ($flgSend)
		{
			//echo "Email Sending.";
		}
	}
	
}
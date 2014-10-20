<?php
require_once (realpath(dirname(__FILE__))."/Core.String.php");
require_once (realpath(dirname(__FILE__))."/class.phpmailer.php");
require_once (realpath(dirname(__FILE__))."/Core.Configuration.php");
require_once (realpath(dirname(__FILE__))."/Core.Configuration.ConfigurationManager.php");

class Email
{
	
	public static function Send($from, $to, $cc="", $bcc="", $subject, $message)
	{
		$conf				= new ConfigurationManager();
		$MailComponentType	= $conf->GetAttribute("MailComponentType");
		
		switch(strtoupper($MailComponentType))
		{
			case "MAIL":
				Email::Mail_Send($from, $to, $cc, $bcc, $subject, $message);
				break;
			case "SMTPMAILER":
				Email::PHPMailer_Send($from, $to, $cc, $bcc, $subject, $message);
				break;
			default:
				die("Invalide configuration 'MailComponentType'.");
		}
		
		return true;	
	}

 
	private static function PHPMailer_Send($from, $to, $cc="", $bcc="", $subject, $message)
	{
		$conf = new ConfigurationManager();
		$SMTPServer = $conf->GetAttribute("SMTPServer");
		$SMTPUsername = $conf->GetAttribute("SMTPUsername");
		$SMTPPassword = $conf->GetAttribute("SMTPPassword");

		$mail = new PHPMailer();
		$mail->CharSet = "utf-8";
		$mail->IsSMTP();
		$mail->Host = $SMTPServer;
		$mail->Port = 25;
		$mail->SMTPAuth = true;
		$mail->Username = $SMTPUsername;
		$mail->Password = $SMTPPassword;
		$mail->SetFrom($from);

		if (!empty($cc))
		{
			$cc = str_replace(",",";",$cc);
			$cc = explode(";", $cc);
			foreach ($cc as $key => $value)
			{
				$mail->AddCC($value);
			}
		}
		if (!empty($bcc))
		{
			$bcc = str_replace(",",";",$bcc);
			$bcc = explode(";", $bcc);
			foreach ($bcc as $key => $value)
			{
				$mail->AddBCC($value);
			}
		}

		//$to = str_replace(",",";",$to);
		//$to = explode(";", $to);
		
		if (!is_array($to))
			$mail->AddAddress($to);
		else
		{
			foreach ($to as $key => $value)
			{
				$mail->AddAddress($value);
			}
		}
		$mail->Subject = $subject;
		//$mail->Body = $message;
		$mail->MsgHTML($message);

		if ($mail->Send())
			return true;			// send complete
		else
			return false;
	}

	private static function Mail_Send($mail_from, $mail_to, $mail_cc="", $mail_bcc="", $mail_subject, $mail_message)
	{
		if (!function_exists('mail'))
			return false;

		if (is_array($mail_cc))
			$str_cc = implode(",", $mail_cc);
		else
			$str_cc = $mail_cc;

		if (is_array($mail_bcc))
			$str_bcc = implode(",", $mail_bcc);
		else
			$str_bcc = $mail_bcc;
		
		$mail_header  = "MIME-Version: 1.0\r\n";
		$mail_header .= "Content-type: text/html; charset=utf-8\r\n";
		$mail_header .= "From: $mail_from\r\n";
		if (!empty($mail_bcc))
			$mail_header .= "Bcc: $str_bcc\r\n";
		if (!empty($mail_cc))
			$mail_header .= "Cc: $str_cc\r\n";

		$flgSend = @mail($mail_to, $mail_subject, $mail_message, $mail_header);
		if ($flgSend)
			return true;
		else
			return false;
	
	}
}

<?php
require_once("dbhelper.php");
require_once("helper.php");

class dbaccess
{
	public static function GetCustomerId($fb_id)
	{
		$customers = self::GetCustomer($fb_id);
		$customerId = $customers['customer_id'];
		if (!empty($customerId))
			return $customerId;
		else
			return "";
	}

	public static function IsCustomer($fb_id)
	{
		$customers = self::GetCustomer($fb_id);
		$fb_id = $customers['fb_id'];
		if (!empty($fb_id))
			return true;
		else
			return false;
	}

	public static function IsRegistered($fb_id)
	{
		$customers = self::GetCustomer($fb_id);
		$status = $customers['status'];
		if ($status==1)
			return true;
		else
			return false;
	}

	public static function GetCustomer($fb_id)
	{
		if (empty($fb_id))
			return false;

		if (!is_numeric($fb_id))
			return false;

		$sql = " SELECT 
						customer_id, fb_forename, fb_lastname, fb_email, status, fb_id, is_like, is_skip_fan
					FROM 
						customers 
					WHERE
						fb_id = $fb_id
						AND status in (1,9) 
				";
		return ExecQuery($sql,"one");
	}

	public static function AddCustomer($fb_id, $fb_fname, $fb_lname, $fb_gender, $fb_bdate, $fb_email, $is_existing_fan)
	{
		if (empty($fb_id))
			return false;

		if (!is_numeric($fb_id))
			return false;

		$id = 0;
		$com_create_guid = Helper::GetGUID();

		$fb_fname = addslashes($fb_fname);
		$fb_lname = addslashes($fb_lname);
		$fb_gender = addslashes($fb_gender);

		$sql = "INSERT INTO customers  					
					(fb_id, fb_forename ,fb_lastname, fb_gender, fb_dob, fb_email
					, status, create_date, update_date	, is_like, is_existing_fan, is_skip_fan, reference_code)  
				VALUES 
					('$fb_id', '$fb_fname', '$fb_lname', '$fb_gender', '$fb_bdate' , '$fb_email'
					, '9', NOW(), NOW()	, '$is_existing_fan' , '$is_existing_fan', '0' , '$com_create_guid') ";
		$id = ExecNonQuery($sql);
		return $id;
	}

	public static function UpdateCustomer($customer_id, $forename="", $lastname="", $email="", $mobileno="", $gender="", $dob="", $address_1="", $address_2="", $address_3="", $city="", $postcode="")
	{
		if (empty($customer_id))
			return false;

		$sql = "
				UPDATE customers SET
					  update_date = NOW() ".
					(empty($forename)	? "" :  ", forename = '$forename' ").
					(empty($lastname)	? "" :  ", lastname = '$lastname' ").
					(empty($email)		? "" :  ", email = '$email' ").
					(empty($mobileno)	? "" :  ", mobileno = '$mobileno' ").
					(empty($gender)		? "" :  ", gender = $gender ").
					(empty($dob)		? "" :  ", dob = '$dob' ").
					(empty($address_1)	? "" :  ", address_1 = '$address_1' ").
					(empty($address_2)	? "" :  ", address_2 = '$address_2' ").
					(empty($address_3)	? "" :  ", address_3 = '$address_3' ").
					(empty($city)		? "" :  ", city = '$city' ").
					(empty($postcode)	? "" :  ", postcode = '$postcode' ").
					", status = 1 
				WHERE 
					customer_id = $customer_id
			";
		return ExecNonQuery($sql);
	}

	public static function UpdateCustomerHomeTown($fb_id, $hometown)
	{
		if (empty($fb_id) || empty($hometown))
			return false;

		if (!is_numeric($fb_id))
			return false;

		$sql = "UPDATE customers SET
					fb_hometown = '$hometown' 
					, update_date = NOW()
				WHERE fb_id = $fb_id
			";
		ExecNonQuery($sql);
		return "";
	}

	public static function UpdateCustomerLocation($fb_id, $current_location)
	{
		if (empty($fb_id) || empty($current_location))
			return false;

		if (!is_numeric($fb_id))
			return false;

		$sql = "UPDATE customers SET
					fb_current_location = '$current_location' 
					, update_date = NOW()
				WHERE fb_id = $fb_id ";
		ExecNonQuery($sql);
		return "";
	}

	public static function UpsertCustomerSigs($fb_id ,$session_key ,$secret_key ,$access_token)
	{
		if (empty($fb_id))
			return false;

		if (!is_numeric($fb_id))
			return false;

		$sql = "
				INSERT INTO customer_sigs   					
					(fb_id, session_key, secret_key, access_token, create_date , update_date)  
				VALUES 
					('$fb_id','$session_key','$secret_key','$access_token', NOW(), NOW()) 
				ON DUPLICATE KEY UPDATE 
						session_key = '$session_key'
					,	secret_key = '$secret_key' ". 
						(empty($access_token) ? "" : ",	access_token = '$access_token' ").
					",	update_date = NOW()
				";
				//					,   LOGIN_ROUND = (LOGIN_ROUND+1)

		ExecNonQuery($sql);
	}

	public static function IsLike($fb_id)
	{
		$customers = self::GetCustomer($fb_id);
		$fb_id = $customers['is_like'];
		if (!empty($fb_id) && $fb_id == 1)
			return true;
		else
			return false;
	}

	public static function IsSkipFan($fb_id)
	{
		$customers = self::GetCustomer($fb_id);
		$is_skip_fan = $customers['is_skip_fan'];
		if (!empty($is_skip_fan) && $is_skip_fan == 1)
			return true;
		else
			return false;
	}

	public static function UpdateIsLike($fb_id, $is_like)
	{
		if (empty($fb_id))
			return false;

		$sql = "UPDATE customers SET
					is_like = '$is_like' 
					, update_date = NOW()
				WHERE fb_id = $fb_id ";
		ExecNonQuery($sql);
		return "";
	}

	public static function UpdateIsSkipFan($fb_id, $is_skip_fan)
	{
		if (empty($fb_id))
			return false;

		$sql = "UPDATE customers SET
					is_skip_fan = '$is_skip_fan' 
					, update_date = NOW()
				WHERE fb_id = $fb_id ";
		ExecNonQuery($sql);
		return "";
	}






	public static function AddInvitationLogs($fb_id, $invite_fb_id, $request_ids)
	{
		if (empty($fb_id) || empty($invite_fb_id))
			return false;

		$sql = "INSERT INTO invitation_logs  					
						(fb_id, invite_fb_id, request_ids, create_date)
					VALUES 
						('$fb_id', '$invite_fb_id', '$request_ids', NOW()) 
				";
		$id = ExecNonQuery($sql);
		return $id;
	}

	public static function AddRequestidsLogs($request_ids, $from_id, $to_id, $data)
	{
		if (empty($request_ids) || empty($from_id) || empty($to_id))
			return false;

		$status = 1;

		$sql = "INSERT INTO requestids_logs  					
						(request_ids, from_id, to_id, data, status, create_date)
					VALUES 
						('$request_ids', '$from_id', '$to_id', '$data', $status, NOW()) 
				";
		$id = ExecNonQuery($sql);
		return $id;
	}

	public static function GetCustomerSigs($fb_id)
	{
		if (empty($fb_id))
			return "";

		$sql = "SELECT access_token FROM customer_sigs WHERE fb_id = $fb_id";
		$result = ExecQuery($sql, "one");
		if(empty($result))
			return "";
		return $result["access_token"];
	}

	public static function AddCustomerActionLog($fb_id, $customer_id, $action_code, $page_name, $point_of_action, $transition_to)
	{
		$ip_address = Helper::GetIP();

		$sql = "INSERT INTO customer_action_log 
			(customer_id, fb_id, action_code, page_name, point_of_action, transition_to, ip, status, create_date) 
				VALUES
			($customer_id, $fb_id, '$action_code', '$page_name', '$point_of_action', '$transition_to', '$ip_address', 1, NOW())
		";
		
		$id = ExecNonQuery($sql);
		return $id;
	}

	public static function AddCustomerShare($fb_id, $post_id)
	{
		$sql = "INSERT INTO customer_share 
			(fb_id, post_id, status, create_date, create_by)
				VALUES
			($fb_id, $post_id, 1, NOW(), 1)
		";
		
		$id = ExecNonQuery($sql);
		return $id;
	}


}		//  end of class dbaccess
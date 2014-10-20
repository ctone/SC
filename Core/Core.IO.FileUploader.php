<?php
 
require_once(realpath(dirname(__FILE__))."/Core.Configuration.php");
require_once(realpath(dirname(__FILE__))."/Core.String.php");
require_once(realpath(dirname(__FILE__))."/Core.Database.MySQL.php");
require_once(realpath(dirname(__FILE__))."/Core.Image.php");
require_once(realpath(dirname(__FILE__))."/Core.GUID.php");

Class FileUploader
{

	public static function SaveFile(
		$conf_path, 
		$html_file_upload_object, 
		$create_thumbnail = false, 
		$thumbnail_max_width = 150, 
		$thumbnail_max_height =150){
				
					
		if(!isset($conf_path))
			die("ERROR: 'conf_path' is empty.");
 
		if(!isset($html_file_upload_object) || count($html_file_upload_object) == 0)
			die("ERROR: 'html_file_upload_object' Object reference not set.");
		
 		$new_file_names = "";
		
        $file_count = count($html_file_upload_object["name"]);

		if (is_array($html_file_upload_object["name"]))
		{
			for ($index=0; $index<=$file_count-1; $index++)
			{
					//echo "name:".$html_file_upload_object["name"][$index]."<HR>";
					if($html_file_upload_object["size"][$index] > 0)
					{
					$new_file_name = FileUploader::SaveUploadFile(
						$html_file_upload_object["name"][$index], 
						$html_file_upload_object["tmp_name"][$index], $conf_path,
						$create_thumbnail, 
						$thumbnail_max_width, 
						$thumbnail_max_height);
						$new_file_names[] = $new_file_name;
					}			
			}
		}
		else
		{
			$new_file_name = FileUploader::SaveUploadFile(
				$html_file_upload_object["name"], 
				$html_file_upload_object["tmp_name"], $conf_path,
				$create_thumbnail, 
				$thumbnail_max_width, 
				$thumbnail_max_height);
					
			$new_file_names = $new_file_name;
		}

		return $new_file_names;
	}
	
	private static function SaveUploadFile(
		$upload_file_name, 
		$upload_tem_path, 
		$conf_path, 
		$create_thumbnail = false, 
		$thumbnail_max_width  = 150, 
		$thumbnail_max_height = 150){
  
		$conf = new ConfigurationManager();
		$file_path = $conf->GetAttribute($conf_path);

		$new_file_name =  FileUploader::GetFileName($upload_file_name);
		
			
		if(!file_exists($file_path))
			mkdir($file_path); 
		
				//Save file
		$file_name_with_path = $file_path.$new_file_name;
		move_uploaded_file($upload_tem_path,
			$file_name_with_path);

		
		//Save thumbnail
		if($create_thumbnail)
		{
			$thumbnail_file_name = FileUploader::GetThumbnailName($new_file_name);
			$thumbnail_file_name_with_path	= $file_path.$thumbnail_file_name;	
			Image::ResizeImageWithAutoScale(
				$file_name_with_path, 
				$thumbnail_file_name_with_path, 
				$thumbnail_max_width, 
				$thumbnail_max_height);			
			
 
			return array("image" => $new_file_name, 
				"thumbnail" => $thumbnail_file_name,
				"original" => $upload_file_name);
			
		}
		

		//without thumbnail
		return array("image" =>$new_file_name, "original"=> $upload_file_name);
	}
	
	public static function GetFileName($filename)
	{
		$today = MySQL::Instance()->GetCurrentDate();
		$today = str_replace(":", "", $today);
		$today = str_replace("-", "", $today);
		$today = str_replace(" ", "", $today);

		//$new_file_name =  String::Format("{0}_{1}",
		//	$today,
		//	$filename);
		
	
		//$new_file_name =  htmlspecialchars($new_file_name, ENT_COMPAT, "UTF-8");
		//$new_file_name_encoding_converted = iconv('utf-8','windows-874',$new_file_name);	
		//return $new_file_name_encoding_converted;

		$guid = GUID::NewGUID();
		$guid = str_replace("{", "", $guid);
		$guid = str_replace("}", "", $guid);

		$tmps = explode(".", $filename);
		$file_extension = $tmps[count($tmps)-1];

		$new_file_name =  String::Format("{0}_{1}.{2}",
			$today,
			$guid,
			$file_extension);

		
		return $new_file_name;
	}
	
	private static function GetThumbnailName($filename)
	{
		$tmps = explode(".", $filename);
		$filename		= "";
		$index = 0;
		foreach ($tmps as $key=> $value)
		{
			if($index < count($tmps)-1)
			{
				if($index == 0)
					$filename = $value;
				else
					$filename .=".".$value;
			} 
			$index++;
		}
	 

		$file_extension = $tmps[count($tmps)-1];

		$thumbnail_file_name = String::Format(
			"{0}_thumb.{1}", 
			$filename,
			$file_extension);
		
		return $thumbnail_file_name;
	}
}

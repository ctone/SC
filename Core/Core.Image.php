<?php

class Image
{
	public static function ResizeImageWithAutoScale($src, $dst, $max_width, $max_height)
	{
		if(!list($width, $height)= getimagesize($src)) return "Unsupported picture type!";
		
		// Proportionally resize the image to the
		// max sizes specified above
		
		$x_ratio = $max_width / $width;
		$y_ratio = $max_height / $height;
		
		if( ($width <= $max_width) && ($height <= $max_height) )
		{
			$tn_width = $width;
			$tn_height = $height;
		}
		elseif (($x_ratio * $height) < $max_height)
		{
			$tn_height = ceil($x_ratio * $height);
			$tn_width = $max_width;
		}
		else
		{
			$tn_width = ceil($y_ratio * $width);
			$tn_height = $max_height;
		}
		// Increase memory limit to support larger files

		return	Image::ResizeImage($src, $dst, $tn_width, $tn_height);
	}
	
	public static function ResizeImageAsPercentage($src,$dst, $percent_resizing)
	{
		$width	= null;
		$height	= null;
	 
		
		if(!list($width, $height)= getimagesize($src)) return "Unsupported picture type!";

		$new_width = round((($percent_resizing/100) * $width));
		$new_height = round((($percent_resizing/100) * $height));
		
		return	Image::ResizeImage($src, $dst, $new_width, $new_height);
	}
	
	public static function ResizeImage($src, $dst, $width, $height, $crop=0)
	{
		try
		{
				ini_set("memory_limit","64M");

			if(!list($w, $h) = getimagesize($src)) return false;// "Unsupported picture type!";
		
				$type = strtolower(substr(strrchr($src,"."),1));
				if($type == 'jpeg') $type = 'jpg';

				switch($type){
					case 'bmp':		$img = imagecreatefromwbmp($src); break;
					case 'gif':		$img = imagecreatefromgif($src); break;
					case 'jpg':		$img = imagecreatefromjpeg($src); break;
					case 'png':		$img = imagecreatefrompng($src); break;
					default : return "Unsupported picture type!";
				}
		
				// resize
				if($crop){
				if($w < $width or $h < $height) return false;// "Picture is too small!";
					$ratio = max($width/$w, $height/$h);
					$h = $height / $ratio;
					$x = ($w - $width / $ratio) / 2;
					$w = $width / $ratio;
				}
				else{
				if($w < $width and $h < $height) return false;// "Picture is too small!";
					$ratio = min($width/$w, $height/$h);
					$width = $w * $ratio;
					$height = $h * $ratio;
					$x = 0;
				}
		
				$new = imagecreatetruecolor($width, $height);
		
				// preserve transparency
				if($type == "gif" or $type == "png"){
					imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
					imagealphablending($new, false);
					imagesavealpha($new, true);
				}
		
				imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);

				switch($type){
					case 'bmp': imagewbmp($new, $dst); break;
					case 'gif': imagegif($new, $dst); break;
					case 'jpg': imagejpeg($new, $dst); break;
					case 'png': imagepng($new, $dst); break;
				}
				
		}catch(exception $e){
			//TODO: keep error log.
			return false;	
		}

		return true;
	}
	

}
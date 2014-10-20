<?php

class String
{
	public static function DetectXSS($text)
	{
		$pattern = array( 
			'@<#script[^>]*?.*?</script>@siu',     
			'@<script[^>]*?.*?</script>@siu',
			'#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i',
			'#(?:on[a-z]+|xmlns)\s*=\s*[\'"\x00-\x20]?[^\'>"]*[\'"\x00-\x20]?\s?#iu',
			'#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu',
			'#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu',
			'#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u',
			'#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#is',
			'#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#is',
			'#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#ius',
			'#</*\w+:\w[^>]*+>#i',
			"'[<]script.*?/script[>]'is");
		
		return String::MatchWith($pattern, $text);	
	}
	
	public static function MatchWith($patterns, $text)
	{
		foreach ($patterns as $key => $pattern)
		{ 
			if (preg_match($pattern, strtolower($text))) 
			{
				return true;
			}
		} 
		return false;
	}
	
	public static function Format() { 
		$args = func_get_args(); 
		if (count($args) == 0) { 
			return; 
		} 
		if (count($args) == 1) { 
			return $args[0]; 
		} 
		$str = array_shift($args); 
		foreach($args as $key=>$value)
		{
			if(!isset($value))
				$args[$key]="";
			
			if($value == 0)
			{
				//in PHP integer value 0 == null
			}elseif($value == null)
			{
				$args[$key]	="";
			}
		}

		$str = preg_replace_callback('/\\{(0|[1-9]\\d*)\\}/', create_function('$match', '$args = '.var_export($args, true).'; return isset($args[$match[1]]) ? $args[$match[1]] : $match[0];'), $str); 
		return $str; 
	} 

	public static function StartWith($Haystack, $Needle){
		// Recommended version, using strpos
		return strpos($Haystack, $Needle) === 0;
	}
	
	/*// Another way, using substr
	public static function StartsWithOld($Haystack, $Needle){ 
		return substr($Haystack, 0, strlen($Needle)) == $Needle;
	}	
	*/
	public static function IndexOf($Heystack, $Needle)
	{
		return strpos($Heystack, $Needle); 	
	}
	
	public static function RemoveHtml( $text )
	{    
		$text = preg_replace(        
					array(          // Remove invisible content 
						'@<head[^>]*?>.*?</head>@siu',            
						'@<style[^>]*?>.*?</style>@siu',            
						'@<script[^>]*?.*?</script>@siu',            
						'@<object[^>]*?.*?</object>@siu',            
						'@<embed[^>]*?.*?</embed>@siu',            
						'@<applet[^>]*?.*?</applet>@siu',            
						'@<noframes[^>]*?.*?</noframes>@siu',            
						'@<noscript[^>]*?.*?</noscript>@siu',            
						'@<noembed[^>]*?.*?</noembed>@siu',          
						// Add line breaks before and after blocks            
						'@</?((address)|(blockquote)|(center)|(del))@iu',            
						'@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',            
						'@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',            
						'@</?((table)|(th)|(td)|(caption))@iu',            
						'@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',            
						'@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',            
						'@</?((frameset)|(frame)|(iframe))@iu',
						'@</?((hr)|(br))@iu',
					),        
					array(
						'**',
						' ', 
						' ', 
						' ', 
						' ', 
						' ', 
						' ', 
						' ', 
						' ', 
						' ',
						"$0", 
						"$0", 
						"$0", 
						"$0", 
						"$0", 
						"$0",
						"$0", 
						"$0", 
						"$0",), 
					$text );      
			// you can exclude some html tags here, in this case B and A tags  
		$text = str_replace("&nbsp;"," ",$text);
		return strip_tags($text);
	}

}
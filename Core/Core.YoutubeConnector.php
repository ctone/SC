<?php
 
require_once("Core.Configuration.php");

require_once  realpath(dirname(__FILE__)).'/../Zend/Loader.php'; 	// the Zend dir must be in your include_path 
require_once realpath(dirname(__FILE__)).'/../Zend/Gdata/YouTube.php';

Zend_Loader::loadClass('Zend_Gdata_YouTube');
Zend_Loader::loadClass('Zend_Gdata_AuthSub'); 
Zend_Loader::loadClass('Zend_Gdata_ClientLogin'); 
Zend_Loader::loadClass('Zend_Gdata'); 
Zend_Loader::loadClass('Zend_Gdata_Query');   
Zend_Loader::loadClass('Zend_Gdata_YouTube_VideoEntry'); 


class YoutubeConnector
{
	public static function Instance()
	{
		return new YoutubeConnector();
	}
	
	function __construct()
	{
		Configuration::Initializing();	

	}
	 
	private function GetYouTubeHttpClient()
	{
		$httpClient	= Zend_Gdata_ClientLogin::getHttpClient(               
			$username		= Configuration::$YOUTUBE_USER,      
			$password		= Configuration::$YOUTUBE_PASSWORD,
			$service		= 'youtube',               
			$client			= null,               
			$source			= Configuration::$YOUTUBE_APP_ID,            
			$loginToken		= null,               
			$loginCaptcha	= null,
			Configuration::$YOUTUBE_AUTHENTICATION_URL);

		return $httpClient;
	}
	public function UploadVideo($file_path, $title, $description) 
	{
		$httpClient		= YoutubeConnector::Instance()->GetYouTubeHttpClient();
		$developerKey	= Configuration::$YOUTUBE_DEVELOPER_KEY; 
		$applicationId	= Configuration::$YOUTUBE_APP_ID;
		$clientId		= '';

		$yt				= new Zend_Gdata_YouTube($httpClient, $applicationId, $clientId, $developerKey);
		$myVideoEntry	= new Zend_Gdata_YouTube_VideoEntry();
		$filesource		= $yt->newMediaFileSource($file_path);
		$contentType	= $this->GetContentType($file_path);
		$filesource->setContentType($contentType);
		$filesource->setSlug($file_path);  //filename only
		$myVideoEntry->setMediaSource($filesource);
		$myVideoEntry->setVideoTitle($title);
		$myVideoEntry->setVideoDescription($description);
		$myVideoEntry->setVideoCategory(Configuration::$YOUTUBE_VIDEO_CATEGORY);

		$VideoTags		= "Samsung, Contest, Samsung Contest, Submit Idea, Idea";
		$myVideoEntry->SetVideoTags($VideoTags);

		// Optionally set some developer tags
		//$myVideoEntry->setVideoDeveloperTags($VideoTags);

		// Optionally set the video's location
		$yt->registerPackage('Zend_Gdata_Geo');
		$yt->registerPackage('Zend_Gdata_Geo_Extension');
		$where = $yt->newGeoRssWhere();
		$position = $yt->newGmlPos('37.0 -122.0');
		$where->point = $yt->newGmlPoint($position);
		$myVideoEntry->setWhere($where);

		try 
		{   
			set_time_limit(0); 
			$newEntry = $yt->insertEntry($myVideoEntry, Configuration::$YOUTUBE_UPLOAD_URL, 
				'Zend_Gdata_YouTube_VideoEntry'); 
			set_time_limit(60); 
			return $newEntry->getVideoId(); 
		} 
		catch (Zend_Gdata_App_HttpException $httpException) 
		{   
			echo $httpException->getRawResponseBody(); 
		} 
		catch (Zend_Gdata_App_Exception $e) 
		{     
			echo $e->getMessage(); 
		}
		return '';
	}
	
	
	
	function GetContentType($filename) 
	{     
		$contentType = ''; 
		$filename = strtolower($filename) ; 
		//	$exts = preg_split("[/\\.]", $filename) ; 
		$exts = explode(".", $filename) ; 
		$n = count($exts)-1; 
		$exts = $exts[$n];
		$exts = strtolower($exts);
		
		switch ($exts) 
		{
			case 'mpeg';
			case 'mpg';
				$contentType = 'video/mpeg'; break;
				break;
			case 'mov';
			case 'qt';
				$contentType = 'video/quicktime'; break;
				break;
			case 'flv': $contentType = 'video/x-flv'; break;
			case 'avi': $contentType = 'video/x-msvideo'; break;
			case 'wmp': $contentType = 'video/x-ms-wmp'; break;
			case 'wmv': $contentType = 'video/x-ms-wmv'; break;
			case 'movie': $contentType = 'video/x-sgi-movie'; break;
			case 'mp4': $contentType = 'video/mp4'; break;
			//Add More ConTentType
		}
		return $contentType;
	} 
	
	public function GetVideoThumbnailByVID($videoId){
		$videoEntry = YoutubeConnector::Instance()->GetVideoEntry($videoId);
		return YoutubeConnector::Instance()->GetThumbnailByVideoEntry($videoEntry);
	}
	
	public function GetThumbnailByVideoEntry($videoEntry){
		$thum="";
		if ($videoEntry != null)
		{
			//$thum = $videoEntry->getVideoThumbnails();  
			$videoThumbnails = $videoEntry->getVideoThumbnails();   				
			if ( $videoThumbnails != null &&  count($videoThumbnails) > 0 )
			{
				$thum = $videoThumbnails[count($videoThumbnails)-1]['url'];
			}
		}
		return $thum;
	}
	
	
	//return VideoEntry
	public function GetVideoEntry($videoId){
		$httpClient		= YoutubeConnector::Instance()->GetYouTubeHttpClient();
		$developerKey	= Configuration::$YOUTUBE_DEVELOPER_KEY; 
		$applicationId	= Configuration::$YOUTUBE_APP_ID;
		$clientId		= '';
		$yt				= new Zend_Gdata_YouTube($httpClient, $applicationId, $clientId, $developerKey);

		set_time_limit(0);
		$videoEntry		= $yt->GetVideoEntry($videoId);
		set_time_limit(60); 
		return $videoEntry;
	}
	
	/*public  function IsExistingVideo($vdo_name)
	{
		//http://youtu.be/ULINjXscBeU		
		$vdo_name ="romanza 2011-10-31";
		 
			$httpClient =    Zend_Gdata_ClientLogin::getHttpClient
			(               
				$username = Configuration::$YOUTUBE_USER,      
				$password = Configuration::$YOUTUBE_PASSWORD,
				$service = 'youtube',               
				$client = null,               
				$source = Configuration::$YOUTUBE_APP_ID,            
				$loginToken = null,               
				$loginCaptcha = null,
				Configuration::$YOUTUBE_AUTHENTICATION_URL
				);
			
			$yt = new Zend_Gdata_YouTube($httpClient, 
							Configuration::$YOUTUBE_APP_ID , 
							Configuration::$YOUTUBE_APP_ID, 
							Configuration::$YOUTUBE_DEVELOPER_KEY);
						
			//$videoFeed = $yt->getVideoResponseFeed($vdo_id);
			$query = $yt->newVideoQuery();
			$query->videoQuery = urlencode($vdo_name);
			$query->category = 'Entertainment';
			$videoFeed = $yt->getVideoFeed($query);
		echo strlen($videoFeed)."<HR>";
		
		if(isset($videoFeed))
			return false;
		else 
			return true;
		//foreach ($videoFeed as $videoEntry) {
		//	echo "---------VIDEO----------\n";
		//	echo "Title: " . $videoEntry->getVideoTitle() . "\n";
		//	echo "\nDescription:\n";
		//	echo $videoEntry->getVideoDescription();
		//	echo "\n\n\n";
		//}
	}*/
	 
}
<?php
require_once(realpath(dirname(__FILE__))."/../Core/Core.Database.MySQL.php");
require_once(realpath(dirname(__FILE__))."/../Core/Core.IO.FileUploader.php");
require_once(realpath(dirname(__FILE__))."/../Core/Core.Http.php");
class ProductDB
{
	/*public function __construct()
	{}*/
	public static function Instance()
	{
		return new ProductDB();
	}
		
	public function GetAllProduct()
	{
		$MySQL = MySQL::Instance();
		$sql = "SELECT * FROM user";
		
		return $MySQL->ExecQuery($sql);  
	}
	public function GetRSSDailyNews($limit)
	{
		
		$dateFrom = getdate(mktime(0,0,0,date("n"),date("j"),date("Y")));
		$dateTo = getdate(mktime(23,59,59,date("n"),date("j"),date("Y")));
		$dateFrom = date("Y-n-j G:i:s", $dateFrom[0]);
		$dateTo = date("Y-n-j G:i:s",  $dateTo[0]);
		
		$MySQL = MySQL::Instance();
		$sql = "
		SELECT ct.name AS title, ct.description, CONCAT('https://www.honda.asia/detail.php?ctid=', ct.id) AS link, ct.publish_date, COALESCE(rl.company_name, '') AS company
		FROM contents ct
		LEFT JOIN related_links rl
			ON ct.company_id = rl.id
		WHERE 
			ct.status = 1
		AND RTRIM(ct.code) = ''
		AND ct.publish_date BETWEEN '$dateFrom' AND '$dateTo'
		ORDER BY COALESCE(ct.publish_date, 0) DESC " .
			(empty($limit) ? "" : "LIMIT " . $limit);
		
		return $MySQL->ExecQuery($sql);  
	}
	public function GetLatestNews()
	{
		$ip = Http::GetIP();
		$MySQL = MySQL::Instance();
		$sql = "								 
		SELECT 
			DISTINCT con.*
			, refcou.code as country_code
			, refcou.flag
			, cml.languages
			, CASE WHEN ul.id IS NOT NULL THEN 1 ELSE 0 END AS is_saw
		FROM contents con
		INNER JOIN ref_country  refcou ON refcou.id = con.origin_id
		LEFT JOIN (
			SELECT content_id, GROUP_CONCAT(language_code SEPARATOR '|') AS languages FROM contents_ml WHERE status = 1 GROUP BY content_id
		) cml ON con.id = cml.content_id
		LEFT JOIN 
		(
			SELECT id, content_id 
			FROM user_action_log 
			WHERE ip = '$ip'
		) ul 
		ON ul.content_id = con.id
		WHERE 
			con.status = 1
		AND RTRIM(con.code) = ''
		AND DATE(con.publish_date) <= DATE(NOW())
		ORDER BY COALESCE(con.publish_date, 0) DESC
		LIMIT 0,6";
		
		return $MySQL->ExecQuery($sql);
	}
	public function GetNewsCountByDateRange($dateFrom, $dateTo)
	{
		$dateFrom = addslashes($dateFrom);
		$dateTo = addslashes($dateTo);

		$MySQL = MySQL::Instance();
		$sql = "
		SELECT COUNT(1) AS cnt
		FROM contents ct
		WHERE 
			ct.status = 1
		AND RTRIM(ct.code) = ''
		AND DATE(ct.publish_date) <= DATE(NOW())
		AND	ct.publish_date BETWEEN '$dateFrom' AND '$dateTo'";
		return $MySQL->ExecScalar($sql);
	}
	public function GetNewsByDateRange($dateFrom, $dateTo, $page=1, $rowPerPage=0)
	{
		$dateFrom = addslashes($dateFrom);
		$dateTo = addslashes($dateTo);

		$start = ($page - 1) * $rowPerPage;
		$MySQL = MySQL::Instance();
		$sql = "
		SELECT ct.*
		,	rc.code
		,	rc.flag
		,	COALESCE(mr.related_image_count, 0) AS related_image_count
		,	COALESCE(mr.related_vdo_count, 0) AS related_vdo_count
		,	cml.languages
		FROM contents ct
		LEFT JOIN ref_country rc
			ON ct.origin_id = rc.id
		LEFT JOIN (
			SELECT content_id, GROUP_CONCAT(language_code SEPARATOR '|') AS languages FROM contents_ml WHERE status = 1 GROUP BY content_id
		) cml ON ct.id = cml.content_id
		LEFT JOIN (
			SELECT 
				src.content_id
			,	SUM(CASE WHEN src.media_type = 2 THEN 1 ELSE 0 END) AS related_image_count
			,	SUM(CASE WHEN src.media_type = 3 THEN 1 ELSE 0 END) AS related_vdo_count
			FROM
			(
				SELECT DISTINCT 
					ck.content_id
				,	m.id
				,	rf.media_type
				FROM content_keyword ck
				INNER JOIN media_keyword mk
					ON ck.keyword_id = mk.keyword_id
				INNER JOIN media m
					ON mk.media_id = m.id
				INNER JOIN ref_filetype rf
					ON m.file_type = rf.id
				WHERE 
					mk.status = 1
			) src
			GROUP BY
				src.content_id
		) mr
			ON ct.id = mr.content_id
		WHERE ct.status = 1
		AND RTRIM(ct.code) = ''
		AND DATE(ct.publish_date) <= DATE(NOW())
		AND	ct.publish_date BETWEEN '$dateFrom' AND '$dateTo'
		ORDER BY COALESCE(ct.publish_date, 0) DESC " .
		(empty($rowPerPage) ? "" : "LIMIT $start, $rowPerPage");
		return $MySQL->ExecQuery($sql);
	}
	public function GetPublishDateByDateRange($dateFrom, $dateTo)
	{
		$dateFrom = addslashes($dateFrom);
		$dateTo = addslashes($dateTo);

		$MySQL = MySQL::Instance();
		$sql = "
		SELECT DISTINCT ct.publish_date
		FROM contents ct
		WHERE 
			ct.status = 1
		AND RTRIM(ct.code) = ''
		AND	ct.publish_date BETWEEN '$dateFrom' AND '$dateTo'
		ORDER BY COALESCE(ct.publish_date, 0) ASC ";
		return $MySQL->ExecQuery($sql);
	}
	
	public function GetCompany($contentId)
	{
		if (empty($contentId))
			return;
		
		$MySQL = MySQL::Instance();
		$sql = "SELECT rl.company_name 
				FROM contents c
				LEFT JOIN related_links rl ON c.company_id = rl.id
				WHERE c.id= $contentId 
				AND c.status = 1";
		return $MySQL->ExecQuery($sql);
	}
	
	
	
	public function GetNewsCatalog($until)
	{
		$sql = "
		SELECT DISTINCT MONTH(ct.publish_date) AS publish_month, YEAR(ct.publish_date) AS publish_year
		FROM contents ct
		WHERE ct.publish_date < '$until'
		ORDER BY publish_year DESC, publish_month DESC";
		
		return $MySQL->ExecQuery($sql);
	}

	public function GetNewsByCategoryId($categoryId, $page, $rowPerPage)
	{
		if (!is_numeric($categoryId))
			die("detect sql injection script!");
		if (!is_numeric($rowPerPage))
			die("detect sql injection script!");

		$start = ($page - 1) * $rowPerPage;
		$MySQL = MySQL::Instance();
		$sql = "
		SELECT DISTINCT
			ct.id
		,	ct.code AS content_code 
		,	rc.code
		,	rc.flag
		,	ct.thumbnail
		,	ct.name
		,	ct.short_description
		,	ct.description
		,	ct.publish_date
		,	COALESCE(mr.related_image_count, 0) AS related_image_count
		,	COALESCE(mr.related_vdo_count, 0) AS related_vdo_count
		,	cml.languages
		,	ct.status
		,	ct.company_id
		FROM contents ct
		INNER JOIN content_category cc
			ON ct.id = cc.content_id
		INNER JOIN categories ct1
			ON cc.category_id = ct1.id
		LEFT JOIN ref_country rc
			ON ct.origin_id = rc.id
		LEFT JOIN (
			SELECT content_id, GROUP_CONCAT(language_code SEPARATOR '|') AS languages FROM contents_ml WHERE status = 1 GROUP BY content_id
		) cml
			ON ct.id =cml.content_id
		LEFT JOIN (
			SELECT 
				src.content_id
			,	SUM(CASE WHEN src.media_type = 2 THEN 1 ELSE 0 END) AS related_image_count
			,	SUM(CASE WHEN src.media_type = 3 THEN 1 ELSE 0 END) AS related_vdo_count
			FROM
			(
				SELECT m.id, rf.media_type, ck.content_id
				FROM media m
				INNER JOIN ref_filetype rf
					ON	m.file_type = rf.id
					AND rf.media_type in (2,3)
				INNER JOIN media_keyword mk
					ON	m.id = mk.media_id
				INNER JOIN content_keyword ck
					ON	mk.keyword_id = ck.keyword_id
					
				WHERE 
					m.status = 1
				UNION
				SELECT m.id, rf.media_type, cm.content_id
				FROM media m
				INNER JOIN ref_filetype rf
					ON	m.file_type = rf.id
					AND rf.media_type in (2,3)
				INNER JOIN content_media cm
					ON m.id = cm.media_id
				WHERE
					m.status = 1    
			) src
			GROUP BY
				src.content_id   
		) mr
			ON ct.id = mr.content_id
		WHERE 
			ct.status = 1
		AND cc.status = 1
		AND ct1.status = 1
		AND	(	cc.category_id = $categoryId OR COALESCE(ct1.parent_id, 0) = $categoryId	)
		AND DATE(ct.publish_date) <= DATE(NOW())
		ORDER BY COALESCE(ct.publish_date, 0) DESC
		LIMIT $start, $rowPerPage";

		return $MySQL->ExecQuery($sql);
	}

	public function GetNewsById($contentId, $languageCode="")
	{
		if (empty($contentId))
			return;
		
		$MySQL = MySQL::Instance();
		$sql = "
		SELECT 
			c.id
		,	c.origin_id
		,	COALESCE(cml.name, c.name) AS name
		,	COALESCE(cml.short_description, c.short_description) AS short_description
		,	c.code
		,	COALESCE(cml.description, c.description) AS description
		,	c.menu_id
		,	c.template_id
		,	c.thumbnail
		,	c.is_recommended
		,	c.sorting
		,	c.seo_description
		,	c.seo_keyword
		,	c.publish_date
		,	c.end_date
		,	c.created_date
		,	c.updated_date
		,	c.created_by
		,	c.updated_by
		,	c.status
		,   cml2.languages
		FROM contents c
		LEFT JOIN contents_ml cml
			ON	c.id = cml.content_id
			AND cml.language_code = '$languageCode'
		LEFT JOIN (
			SELECT content_id, GROUP_CONCAT(language_code SEPARATOR '|') AS languages FROM contents_ml WHERE status = 1 GROUP BY content_id
		) cml2 ON c.id =cml2.content_id
		WHERE c.status IN (1, 2)
		AND	c.id = $contentId
		AND DATE(c.publish_date) <= DATE(NOW())
		LIMIT 1";
		
		return $MySQL->ExecQuery($sql);
	}
	public function GetNewsByKeyword($keyword, $page, $rowPerPage)
	{
		if (!is_numeric($rowPerPage))
			die("detect sql injection script!");
		$keyword = addslashes($keyword);

		$MySQL = MySQL::Instance();
		$sql = "
		SELECT ct.* 
		FROM contents ct
		INNER JOIN content_keyword ck
			ON ct.id = ck.content_id
		INNER JOIN keywords kw
			ON ck.keyword_id = kw.id
		WHERE 
			ct.status = 1
		AND ck.status = 1
		AND kw.status = 1
		AND	kw.keyword = '$keyword'
		AND DATE(ct.publish_date) <= DATE(NOW())
		ORDER BY COALESCE(ct.publish_date, 0) DESC
		LIMIT 0,20";
		
		return $MySQL->ExecQuery($sql);
	}
	
	
	
	//Get Multi language
	public function GetNewsMLList($contentId)
	{
		if (!is_numeric($contentId))
			die("detect sql injection script!");

		$MySQL = MySQL::Instance();
		$sql	= "
		SELECT * FROM contents_ml
		WHERE status IN (1, 2)
		AND	  content_id = $contentId
				";
		return $MySQL->ExecQuery($sql);
	}
	public function GetNewsMLListById($contentId, $languageCode)
	{
		if (!is_numeric($contentId))
			die("detect sql injection script!");

		$languageCode = addslashes($languageCode);

		$MySQL = MySQL::Instance();
		$sql = "
		SELECT * FROM contents_ml
		WHERE	status IN (1, 2)
		AND		content_id		= $contentId
		AND		language_code	= '$languageCode'
		";
		return $MySQL->ExecQuery($sql);
	}
	
	//TODO: Confirm business logic with client.
	public function GetRelatedNews($contentId, $number)
	{
		if (!is_numeric($contentId))
			die("detect sql injection script!");
		if (!is_numeric($number))
			die("detect sql injection script!");

		$MySQL = MySQL::Instance();
		$sql = "SELECT DISTINCT c.*, f.flag, cml.languages
		,	COALESCE(mr.related_image_count, 0) AS related_image_count
		,	COALESCE(mr.related_vdo_count, 0) AS related_vdo_count
		FROM content_keyword ck1
		INNER JOIN content_keyword ck2
			ON ck1.keyword_id = ck2.keyword_id
			AND ck1.content_id = $contentId
		INNER JOIN contents c
			ON ck2.content_id = c.id
		LEFT JOIN (
			SELECT content_id, GROUP_CONCAT(language_code SEPARATOR '|') AS languages FROM contents_ml WHERE status = 1 GROUP BY content_id
		) cml
			ON c.id = cml.content_id
		LEFT JOIN ref_country f ON f.id = c.origin_id
		LEFT JOIN (
			SELECT 
				src.content_id
			,	SUM(CASE WHEN src.media_type = 2 THEN 1 ELSE 0 END) AS related_image_count
			,	SUM(CASE WHEN src.media_type = 3 THEN 1 ELSE 0 END) AS related_vdo_count
			FROM
			(
				SELECT DISTINCT 
					ck.content_id
				,	m.id
				,	rf.media_type
				FROM content_keyword ck
				INNER JOIN media_keyword mk
					ON ck.keyword_id = mk.keyword_id
				INNER JOIN media m
					ON mk.media_id = m.id
				INNER JOIN ref_filetype rf
					ON m.file_type = rf.id
				WHERE 
					mk.status = 1
			) src
			GROUP BY
				src.content_id
		) mr
			ON c.id = mr.content_id
		WHERE 
			c.id != ck1.content_id
		AND	RTRIM(c.code) = ''
		AND DATE(c.publish_date) <= DATE(NOW())
		AND c.STATUS = 1
		ORDER BY COALESCE(c.created_date, 0) DESC
		LIMIT 0, $number
		";
		return $MySQL->ExecQuery($sql);
	}
	
	public function SearchNews($keyword, $dateFrom, $dateTo, $origin, $order, $categories, $page, $rowPerPage)
	{
		if (!is_numeric($rowPerPage))
			die("detect sql injection script!");

		$start = ($page - 1) * $rowPerPage;
		
		$searchWords = '';
		$searchWords2 = '';
		if (!empty($keyword))
		{
			$keywords = explode(' ', $keyword);
			$searchWords = implode('\',\'', $keywords);
			$searchWords = '\'' . $searchWords . '\'';
			
			$searchWords2 = implode(',', $keywords);
		}
		
		$searchCategory = '';
		if (!empty($categories))
		{
			$searchCategory = implode(',', $categories);
		}
		
		if (empty($order))
			$order = "DESC";
		elseif (is_array($order))
			$order = $order[0];
		
		if ($dateFrom == "--")
			$dateFrom = "";
		if ($dateTo == "--")
			$dateTo = "";
		
		if(!empty($origin))
			$originSql = "AND c.origin_id = $origin";
		else 
			$originSql = "";
		
		$MySQL = MySQL::Instance();
		$sql = "
		SELECT DISTINCT
			c.id
		,	c.origin_id
		,	c.name
		,	c.short_description
		,	c.description
		,	c.menu_id
		,	c.template_id
		,	c.thumbnail
		,	c.is_recommended
		,	c.publish_date
		,	c.end_date
		,	c.created_date
		,	c.created_by
		,	c.status
		,	f.flag
		,	cml.languages
		,	COALESCE(mr.related_image_count, 0) AS related_image_count
		,	COALESCE(mr.related_vdo_count, 0) AS related_vdo_count
		,	c.company_id
		,	f.code as country_code
		FROM contents c
		INNER JOIN content_category cc
			ON c.id = cc.content_id
		LEFT JOIN content_keyword ck
			ON ck.content_id = c.id
		LEFT JOIN keywords k
			ON ck.keyword_id = k.id "
			.  (empty($searchWords2) ? "" : ((empty($searchWords) ? "" : "AND k.keyword IN ($searchWords)"))) . "
		LEFT JOIN categories cat1
			ON cc.category_id = cat1.id 	
		LEFT JOIN categories cat2
			ON cat1.parent_id = cat2.id
		LEFT JOIN categories cat3
			ON cat2.parent_id = cat3.id
		LEFT JOIN (
			SELECT content_id, GROUP_CONCAT(language_code SEPARATOR '|') AS languages FROM contents_ml WHERE status = 1 GROUP BY content_id
		) cml
			ON c.id = cml.content_id
		LEFT JOIN ref_country f ON f.id = c.origin_id
		LEFT JOIN (
			SELECT 
				src.content_id
			,	SUM(CASE WHEN src.media_type = 2 THEN 1 ELSE 0 END) AS related_image_count
			,	SUM(CASE WHEN src.media_type = 3 THEN 1 ELSE 0 END) AS related_vdo_count
			FROM
			(
				SELECT DISTINCT 
					ck.content_id
				,	m.id
				,	rf.media_type
				FROM content_keyword ck
				INNER JOIN media_keyword mk
					ON ck.keyword_id = mk.keyword_id
				INNER JOIN media m
					ON mk.media_id = m.id
				INNER JOIN ref_filetype rf
					ON m.file_type = rf.id
				WHERE 
					mk.status = 1
			) src
			GROUP BY
				src.content_id
		) mr
			ON c.id = mr.content_id
		WHERE RTRIM(c.code) = '' 
		".$originSql."
		AND DATE(c.publish_date) <= DATE(NOW()) " .
			(empty($searchCategory) ? "" : "AND (cc.category_id IN ($searchCategory) OR cat1.id IN ($searchCategory) OR cat2.id IN ($searchCategory) OR cat3.id IN ($searchCategory) OR cat3.parent_id IN ($searchCategory))") . " ";
			
		if (!empty($searchWords2))
		{
			$sql .= "AND ( false ";
			foreach ($keywords as $word)
			{
				$sql .= " OR c.name LIKE '%$word%'  ";
			}
			$sql .= " ) ";
		}
		$sql .= " " . (empty($dateFrom) ? (empty($dateTo) ? "" : "AND DATE(c.publish_date) < '$dateTo'") : (empty($dateTo) ? "AND DATE(c.publish_date) = '$dateFrom'" : "AND c.publish_date BETWEEN '$dateFrom' AND '$dateTo'")) . "
			AND c.status = 1
		ORDER BY COALESCE(c.publish_date, 0) $order
		LIMIT $start, $rowPerPage
		";

		return $MySQL->ExecQuery($sql);
	}
			
	public function GetGlobalLinks()
	{
		$MySQL = MySQL::Instance();
		$sql = "
		SELECT company_name, address, url 
		FROM related_links
		WHERE 
			country_id = 13
		AND	status = 1
		AND url != ''
		ORDER BY id";
		
		return $MySQL->ExecQuery($sql);
	}
	
	public function GetCountryById($countryId)
	{
		if (!is_numeric($countryId))
			die("detect sql injection script!");

		$MySQL = MySQL::Instance();
		$sql = "
		SELECT * FROM ref_country
		WHERE status IN (1, 2)
			AND	id = $countryId
		LIMIT 1";
		return $MySQL->ExecQuery($sql);
	}
	
	public static function GetCountryLinks($countryCode)
	{
		$countryCode = addslashes($countryCode);

		$MySQL = MySQL::Instance();
		$sql = "
		SELECT rl.*
		FROM related_links rl
		INNER JOIN ref_country rc
			ON rl.country_id = rc.id
		WHERE 
			LOWER(rc.code) = LOWER('$countryCode')
		AND	rl.status = 1
		ORDER BY rl.id";
		
		return $MySQL->ExecQuery($sql);
	}
	
	public static function GetCompanyList()
	{
		$MySQL = MySQL::Instance();
		$sql = "
		SELECT rl.*
		FROM related_links rl
		WHERE rl.status = 1
		ORDER BY rl.id";
		
		return $MySQL->ExecQuery($sql);
	}

	
	public static function GetBanner($id)
	{
		/*$MySQL = MySQL::Instance();
		$sql = "
		SELECT company_name, address, url 
		FROM related_links
		WHERE 
			country_id = 0
		AND	status = 1
		ORDER BY id";
		
		return $MySQL->ExecQuery($sql);*/
		
		return "";
	}
	
	//Get Other Content 
	public static function GetHelp()
	{	
		$MySQL = MySQL::Instance();
		$sql = "
		SELECT description 
		FROM contents
		WHERE 
			code = 'help'
		AND	status = 1";
		
		return $MySQL->ExecQuery($sql);
	}
	
	public static function GetPolicy()
	{	
		$MySQL = MySQL::Instance();
		$sql = "
		SELECT description 
		FROM contents
		WHERE 
			code = 'policy'
		AND	status = 1";
		
		return $MySQL->ExecQuery($sql);
	}
	
	public static function GetTerm()
	{
		$MySQL = MySQL::Instance();
		$sql = "
		SELECT description, created_date 
		FROM contents
		WHERE code = 'term'
		AND	status = 1";
		
		return $MySQL->ExecQuery($sql);
	}
	
	public function GetMediaAttach($contentId, $language_code)
	{
		if (empty($contentId))
			{return null;}
		if (empty($language_code))
			{$language_code = 0;}
		
		$MySQL = MySQL::Instance();
		$sql = "SELECT id, content_id, filename, original_filename, created_date, updated_date, created_by, updated_by, status
				FROM attachments 
				WHERE content_id = $contentId 
				AND status = 1 
				AND (language_code = '$language_code' OR language_code='')
			";
	
		return $MySQL->ExecQuery($sql);
	}
	
	public function UpdateContent($content_id
		, $origin_id, $name="", $sub_name="", $code="", $description=""
		, $menu_id=0, $template_id=0, $thumbnail="", $is_recommended=0, $seo_description=""
		, $seo_keyword="", $publish_date="", $end_date="", $updated_by=0, $status)
	{
		if (!is_numeric($content_id))
			die("detect sql injection script!");
		if (!is_numeric($origin_id))
			die("detect sql injection script!");
		if (!is_numeric($menu_id))
			die("detect sql injection script!");
		if (!is_numeric($template_id))
			die("detect sql injection script!");
		if (!is_numeric($is_recommended))
			die("detect sql injection script!");
		if (!is_numeric($updated_by))
			die("detect sql injection script!");
		if (!is_numeric($status))
			die("detect sql injection script!");

		$name = addslashes($name);
		$sub_name = addslashes($sub_name);
		$code = addslashes($code);
		$thumbnail = addslashes($thumbnail);
		$seo_description = addslashes($seo_description);
		$seo_keyword = addslashes($seo_keyword);
		$publish_date = addslashes($publish_date);
		$end_date = addslashes($end_date);

		$sorting = 0;
		$MySQL = MySQL::Instance();
		$sql = "UPDATE contents SET
						origin_id		=	$origin_id
						, name			=	'$name'
						, short_description	=	'$sub_name'
						, code			=	'$code'
						, description	=	'$description'
						, menu_id		=	$menu_id
						, template_id	=	$template_id
						, thumbnail	=	" . (empty($thumbnail) ? "'default.thumb.jpg'" : "'$thumbnail'") . "
						, is_recommended	=	$is_recommended
						, sorting					=	$sorting
						, seo_description	=	'$seo_description'
						, seo_keyword		=	'$seo_keyword'
						, publish_date		=	'$publish_date'
						, end_date				=	'$end_date'
						, updated_date		=	NOW()
						, updated_by			=	$updated_by
						, status			= $status
					WHERE
						id = $content_id
			";
		$data = $MySQL->ExecNonQuery($sql);
		return $data;
	}

	public function AddContent(
		$origin_id, $name="", $sub_name="", $code="", $description=""
		, $menu_id=0, $template_id=0, $thumbnail="", $is_recommended=0, $seo_description=""
		, $seo_keyword="", $publish_date="", $end_date="", $updated_by=0, $status)
	{
		if (!is_numeric($origin_id))
			die("detect sql injection script!");
		if (!is_numeric($menu_id))
			die("detect sql injection script!");
		if (!is_numeric($template_id))
			die("detect sql injection script!");
		if (!is_numeric($is_recommended))
			die("detect sql injection script!");
		if (!is_numeric($updated_by))
			die("detect sql injection script!");
		if (!is_numeric($status))
			die("detect sql injection script!");

		$name = addslashes($name);
		$sub_name = addslashes($sub_name);
		$code = addslashes($code);
		$thumbnail = addslashes($thumbnail);
		$seo_description = addslashes($seo_description);
		$seo_keyword = addslashes($seo_keyword);
		$publish_date = addslashes($publish_date);
		$end_date = addslashes($end_date);

		$sorting = $this->GetContentLastSorting();
		$MySQL = MySQL::Instance();

		$sql = "INSERT INTO contents
				(origin_id, name, short_description, code, description
				, menu_id, template_id, thumbnail, is_recommended, sorting
				, seo_description, seo_keyword, publish_date, end_date, created_date
				, updated_date, created_by, updated_by, status)
					VALUES
				($origin_id, '$name', '$sub_name', '$code', '$description'
				, $menu_id, $template_id, " . (empty($thumbnail) ? "'default.thumb.jpg'" : "'$thumbnail'") . ", $is_recommended, $sorting
				, '$seo_description', '$seo_keyword', '$publish_date', '$end_date', NOW()
				, NOW(), $updated_by, $updated_by, $status);
			";
		$data = $MySQL->ExecNonQuery($sql);
		return $data;
	}

	public function GetContentLastSorting()
	{
		$MySQL = MySQL::Instance();
		$sql = "SELECT MAX(sorting) + 1 AS sorting FROM contents WHERE status IN(1,2) ";
		$data = $MySQL->ExecScalar($sql);
		return $data;
	}

	public function SaveAttachment($files, $is_resize=false, $resize_width=49, $resize_height=49)
	{
		if (empty($files))
			return null;
		if (is_array($files) && count($files) <= 0)
			return null;

		$conf = new ConfigurationManager();
		$maxsize = $conf->GetAttribute("ContentFilesUploadMaxSize");
		$file_name  = array();
		$flag_save = true;
		$allow_file_type = array("JPG", "PNG", "GIF", "BMP", "JPEG");

		if (!empty($files) && is_array($files["name"]))
		{
			foreach ($files["name"] as $key => $value)
			{
				if ($this->CanUploadFileAttachment($value, $files["size"][$key], $allow_file_type, $maxsize))
					$flag_save = false;
			}
		}
		else
		{
			if ($this->CanUploadFileAttachment($files["name"], $files["size"], $allow_file_type, $maxsize))
				$flag_save = false;
		}
		
		if ($flag_save)
		{
			if (!empty($files) && count($files) > 0)
			{
				if ($is_resize)
					$file_name = FileUploader::SaveFile("ContentFilesUploadFolder", $files, true, $resize_width, $resize_height);
				else
					$file_name = FileUploader::SaveFile("ContentFilesUploadFolder", $files, true);
			}
			return $file_name;
		}
		return null;
	}

	public function CanUploadFileAttachment($filename, $file_size, $allow_file_type, $max_file_size=10240000)
	{
		if (empty($filename))
			return false;
		if (empty($allow_file_type) || !is_array($allow_file_type) || count($allow_file_type) <= 0)
			return false;

		$tmp = explode(".", $filename);
		$file_extension = $tmp[count($tmp)-1];
		if (empty($file_extension))
			return false;

		foreach ($allow_file_type as $key=> $value)
		{
			if ($file_extension != $value)
				return false;
			if ($file_size > $max_file_size)
				return false;
		}
		return true;
	}

	public function AddContentMediaLink($media_id, $content_id, $created_by)
	{
		$status = 1;
		$sql = "INSERT INTO media_attach 
				(media_id, content_id, created_date, updated_date, created_by, updated_by, status) 
					VALUES 
				($media_id, $content_id, NOW(), NOW(), $created_by, $created_by, $status)";
		$MySQL = MySQL::Instance();
		$data = $MySQL->ExecNonQuery($sql);
		return $data;
	}

	public function AddContentAttachment($files, $content_id, $current_login_id)
	{
		if (empty($files) || count($files) <= 0)
			return null;

		if ($files["error"] >0)
			return null;

		$attachment = $this->SaveAttachment($files);
		$image_name = $attachment["image"];
		$image_thumbnail = $attachment["thumbnail"];
		$image_original = $attachment["original"];

		$filename = $files["name"];
		$tmp = explode(".", $filename);
		$file_extension = $tmp[count($tmp)-1];
		$file_type = 3;

		$this->DeleteContentMediaLink($content_id);
		$media_id = $this->AddContentMedia($file_type, $image_original, $hd_original_filename="", $image_thumbnail, $thumbnail_n=""
							, $image_p="", $width_p="", $height_p="", $current_login_id);

		$this->AddContentMediaLink($media_id, $content_id, $current_login_id);
	}


	public function DeleteContentMediaLink($content_id)
	{
		if (!is_numeric($content_id))
			die("detect sql injection script!");

		$sql = "UPDATE media_attach SET status = 0, updated_date = NOW() WHERE content_id = $content_id";
		$MySQL = MySQL::Instance();
		$data = $MySQL->ExecNonQuery($sql);
		return $data;
	}


	public function AddContentMedia($file_type, $original_filename, $hd_original_filename="", $thumbnail_s="", $thumbnail_n=""
		, $image_p="", $width_p="", $height_p="", $create_by)
	{
		if (!is_numeric($create_by))
			die("detect sql injection script!");

		$file_type = addslashes($file_type);
		$original_filename = addslashes($original_filename);
		$hd_original_filename = addslashes($hd_original_filename);
		$thumbnail_s = addslashes($thumbnail_s);
		$thumbnail_n = addslashes($thumbnail_n);
		$image_p = addslashes($image_p);
		$width_p = addslashes($width_p);
		$height_p = addslashes($height_p);

		$status = 1;
		$sql = "
		INSERT INTO media
			(file_type, original_filename, hd_original_filename, thumbnail_s, thumbnail_n
			, image_p, width_p, height_p, status, create_date
			, create_by, update_date, update_by)
		VALUES
			('$file_type', '$original_filename', '$hd_original_filename', '$thumbnail_s', '$thumbnail_n'
			, '$image_p', '$width_p', '$height_p', $status, NOW()
			, $create_by, NOW(), $create_by)
		";
		$MySQL = MySQL::Instance();
		$data = $MySQL->ExecNonQuery($sql);
		return $data;
	}

	public function GetFileTypeIdByExt($extension)
	{
		$extension = addslashes($extension);

		$sql = "SELECT id FROM ref_filetype WHERE media_type = '$extension' ";
		$MySQL = MySQL::Instance();
		$data = $MySQL->ExecScalar($sql);
		return (!empty($data) ? $data : 0);
	}

	public function UpdateContentStatus($content_id, $status)
	{
		if (!is_numeric($content_id))
			die("detect sql injection script!");
		if (!is_numeric($status))
			die("detect sql injection script!");

		$sql = "UPDATE contents SET status = $status, updated_date = NOW() WHERE id = $content_id ";
		$MySQL = MySQL::Instance();
		$data = $MySQL->ExecNonQuery($sql);
		return $data;
	}

	public function UpdateContentPublish($content_id)
	{
		$status = 1;
		return $this->UpdateContentStatus($content_id, $status);
	}

	public function UpdateContentUnPublish($content_id)
	{
		$status = 2;
		return $this->UpdateContentStatus($content_id, $status);
	}

//-----Archive Page-----
	public function GetYearList()
	{
		$MySQL = MySQL::Instance();
		$sql = "
		SELECT YEAR( publish_date) AS year , COUNT( id ) AS content_count
		FROM	contents
		WHERE	status = 1
		AND		RTRIM(code) = ''
		AND		publish_date != '1990-01-01 00:00:00'
		GROUP BY YEAR( publish_date )  ";
		
		return $MySQL->ExecQuery($sql);
	}	

	public function GetNotificationNews($date, $limit=10)
	{
		if (empty($date))
			$date = date("Y-m-d");
		$sql = "
			SELECT id, name
			FROM contents
			WHERE	
				status = 1
				AND publish_date = '$date'
			ORDER BY RAND()
			LIMIT $limit
		";

		$MySQL = MySQL::Instance();
		return $MySQL->ExecQuery($sql);
	}

}
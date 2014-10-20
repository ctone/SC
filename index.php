<?php
session_start();

header("Expires: -1");
header("Cache-Control: no-store, no-cache, must-revalidate");

require_once(realpath(dirname(__FILE__))."/Core/Core.Http.php");
require_once(realpath(dirname(__FILE__))."/Core/Core.String.php");
require_once(realpath(dirname(__FILE__))."/Core/Core.Secure.php");
require_once(realpath(dirname(__FILE__))."/DatabaseCenter/Database.Product.php");


$contentBiz		= new ProductDB();

//Preparing to page navigater(Paging)	
$contentListAll	= $contentBiz->GetAllProduct();
$contentCount	= count($contentListAll);

if (empty($contentListAll)){
	echo "Data not found";
	return;
}
else
{
	foreach ($contentListAll as $key=>$value)
	{
		$product_name = $value["UserName"];
		echo $product_name;
	}
}
<?php

class AgereException
{
	public $ErrorMessage = "";
	public $InnerException = null;
	function __construct($errorMsg)
	{
		$ErrorMessage = $errorMsg;
	}
	
	 		
}
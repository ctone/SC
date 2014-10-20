<?php

class DbStatus
{
	public static $NEW = 9;
	public static $PUBLISH = 1;
	public static $UNPUBLISHED = 2;
	public static $DELETE = 0;

	// Member Status
	public static $DELETED = 0;
	public static $ACTIVE = 1;
	public static $INACTIVE = 2;
	
	//Abuse Status
	public static $ABUSE_DELETE = 0;
	public static $ABUSE_PUBLISH = 1;
	public static $ABUSE_UNPUBLISHED = 2;
}

class ContentVersionStatus
{
	public static $NEW = 9;
	public static $INACTIVE = 2;
	public static $ACTIVE = 1;
	public static $DELETE = 0;
}

class FileUploadType
{
	public static $VIEDO = 1;
	public static $IMAGE = 2;
}

class UserType
{
	public static $ADMIN		= 1;
	public static $MEMBER		= 2;
}

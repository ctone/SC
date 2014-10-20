<?

class Replacer
{
	public static function RemoveHtmlSyntax($text)
	{
		return strip_tags($text);
	}

	public static function EncodeUrl($text)
	{
		return rawurlencode($text);
	}
}

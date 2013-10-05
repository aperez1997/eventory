<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>

 */

namespace Eventory\Utils;


class HttpUtils
{
	public static function DoesHrefMatchDomain($href, $domain)
	{
		$hrefParts = parse_url($href);
		$domainParts = parse_url($domain);
		return $hrefParts['host'] == $domainParts['host'];
	}

	public static function GetDomainFromUrl($url)
	{
		$parse = parse_url($url);
		return $parse['host']; // prints 'google.com'
	}

	public static function CleanUrl($url)
	{
		return preg_replace('/[?&].(*)$/', '', $url);
	}
}
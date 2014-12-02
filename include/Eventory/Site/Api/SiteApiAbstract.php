<?php
/**
 * @author Tony Perez <tonyp@zoosk.com>
 * @copyright Copyright (c) 2007-2014 Zoosk Inc.
 */

namespace Eventory\Site\Api;

abstract class SiteApiAbstract 
{
	protected function send(\Zaphpa_Response $res, $code, $body = null)
	{
		if ($body){
			$res->add($body);
		}
		$res->send($code, 'json');
	}
	
	protected function sendError(\Zaphpa_Response $res, $error, $code = null)
	{
		if (!isset($code)){
			$code = 500;
		}
		$res->add(json_encode(array("error" => $error)));
		$res->send($code, 'json');
	}
}
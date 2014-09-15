<?php
/**
 * @author Tony Perez <tonyp@zoosk.com>
 * @copyright Copyright (c) 2007-2014 Zoosk Inc.
 */

namespace Eventory\Site\Admin;

use Eventory\Objects\Event\Event;
use Eventory\Objects\Performers\Performer;
use Eventory\Storage\iStorageProvider;

class SiteApiAdmin 
{
	protected $store;
	
	public function __construct(iStorageProvider $provider){
		$this->store = $provider;
	}
	
	public function removeEventPerformer(\Zaphpa_Request $req, \Zaphpa_Response$res)
	{
		$eventId = $req->params['event_id'];
		$event = $this->store->loadEventById($eventId);
		if (!$event instanceof Event){
			$this->sendError($res, 'event not found ' . $eventId, 400);
			return;
		}
		
		$perf = $this->store->loadPerformerById($req->params['performer_id']);
		if (!$perf instanceof Performer){
			$this->sendError($res, 'performer not found', 400);
		}
		
		$this->store->removePerformerFromEvent($perf, $event);
		
		$this->send($res, 200);
	}
	
		
	protected function sendError(\Zaphpa_Response $res, $error, $code = null)
	{
		if (!isset($code)){
			$code = 500;
		}
		$res->add(json_encode(array("error" => $error)));
		$res->send($code, 'json');
	}
	
	protected function send(\Zaphpa_Response $res, $code, $body = null)
	{
		if ($body){
			$res->add($body);
		}
		$res->send($code, 'json');
	}
}

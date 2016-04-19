<?php
/**
 * @author Tony Perez <tonyp@zoosk.com>
 * @copyright Copyright (c) 2007-2014 Zoosk Inc.
 */

namespace Eventory\Site\Admin;

use Eventory\Model\EventModel;
use Eventory\Objects\Event\Event;
use Eventory\Objects\Performers\Performer;
use Eventory\Site\Api\SiteApiAbstract;
use Eventory\Storage\iStorageProvider;

class SiteApiAdmin extends SiteApiAbstract
{
	protected $store;
	protected $model;
	
	public function __construct(iStorageProvider $provider, EventModel $model)
	{
		$this->store = $provider;
		$this->model = $model;
	}

	public function removeEventPerformer(\Zaphpa_Request $req, \Zaphpa_Response $res)
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

	public function deletePerformer(\Zaphpa_Request $req, \Zaphpa_Response $res)
	{
		$perf = $this->store->loadPerformerById($req->params['performer_id']);
		if (!$perf instanceof Performer){
			$this->sendError($res, 'performer not found', 400);
        	}
		$this->store->deletePerformer($perf->getId());

		$this->send($res, 200);
	}

	public function markDuplicate(\Zaphpa_Request $req, \Zaphpa_Response $res)
	{
		$perfDupe = $this->store->loadPerformerById($req->params['performer_id_dupe']);
		if (!$perfDupe instanceof Performer){
			$this->sendError($res, 'performer not found', 400);
        }
		$perfReal = $this->store->loadPerformerById($req->params['performer_id_real']);
		if (!$perfReal instanceof Performer){
			$this->sendError($res, 'performer not found', 400);
        }
		
		$this->model->markPerformerDuplicate($perfDupe, $perfReal);
	}

	public function toggleHighlight(\Zaphpa_Request $req, \Zaphpa_Response $res)
	{
		$perf = $this->store->loadPerformerById($req->params['performer_id']);
		if (!$perf instanceof Performer){
			$this->sendError($res, 'performer not found', 400);
		}
		$this->model->togglePerformerHighlight($perf);

		$this->send($res, 200);
	}
}

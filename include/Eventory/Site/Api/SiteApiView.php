<?php
/**
 * @author Tony Perez <tonyp@zoosk.com>
 * @copyright Copyright (c) 2007-2014 Zoosk Inc.
 */

namespace Eventory\Site\Api;

use Eventory\Model\PerformerModel;
use Eventory\Objects\Performers\Performer;
use Eventory\Site\Constants\SitePageParams;
use Eventory\Utils\ArrayUtils;

class SiteApiView extends SiteApiAbstract
{
	public static function CreatePerformerNode(Performer $performer)
	{
		$data = array(
			'id' => $performer->getId(),
			'name' => $performer->getName(),
			'imageUrl' => $performer->getImageUrl(),
			'highlight' => $performer->isHighlighted(),			
			'event_count' => $performer->getEventCount(),
			'updated' => $performer->getUpdated() * 1000,
			'sort_default' => $performer->getSortKey(),
		);
		return $data;
	}
	
	/** @var PerformerModel */
	protected $performerModel;
	
	public function __construct(PerformerModel $performerModel)
	{
		$this->performerModel = $performerModel;
	}

	public function browsePerformers(\Zaphpa_Request $req, \Zaphpa_Response $res)
	{
		// sort by sort key
		$sortParam = ArrayUtils::ValueForKey($req->params, SitePageParams::SORT, Performer::SORT_DEFAULT);
		$performers = $this->performerModel->getPerformersForBrowse($sortParam);
		
		$performers = array_map(function(Performer $p){
				
		}, $performers);
		$body = array('performers' => $performers);
		
		$this->send($req, 200, $body);
	}
}
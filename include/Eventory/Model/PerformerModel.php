<?php
/**
 * @author Tony Perez <tonyp@zoosk.com>
 * @copyright Copyright (c) 2007-2014 Zoosk Inc.
 */

namespace Eventory\Model;

use Eventory\Objects\Performers\Performer;
use Eventory\Storage\iStorageProvider;
use Eventory\Utils\ArrayUtils;

class PerformerModel 
{
	/** @var iStorageProvider */
	protected $store;

	public function __construct(iStorageProvider $store)
	{
		$this->store = $store;
	}

	/**
	 * @param string|null $sortKey
	 * @return Performer[]
	 */
	public function getPerformersForBrowse($sortKey = null)
	{
		$performers = $this->store->loadAllPerformers();

		// sort by sort key
		$performers = $this->handleSort($performers, $sortKey ? $sortKey : Performer::SORT_DEFAULT);

		// remove deleted performers
		$performers = array_filter($performers, array($this, 'notDeleted'));

		return $performers;
	}
	
	protected function handleSort($performers, $sortType)
	{
		$fn = function (Performer $p) use ($sortType) { return $p->getSortKey($sortType); };
		$performers = ArrayUtils::ReindexByFunction($performers, $fn);
		ksort($performers);
		return $performers;
	}
} 
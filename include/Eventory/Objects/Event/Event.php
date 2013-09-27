<?php
/**
 * @author Tony Perez <tonyp@zoosk.com>
 * @copyright Copyright (c) 2007-2013 Zoosk Inc.
 */

namespace Eventory\Objects\Event;

class Event
{
	public $eventIdentifier;
	public $eventUrl;
	public $descrption;

	/** @var array EventAsset */
	public $assets = array();

	public function addAssets($assets)
	{
		$this->assets = array_merge($this->assets, $assets);
	}
}
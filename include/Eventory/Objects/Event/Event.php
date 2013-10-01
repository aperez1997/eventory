<?php
/**
 * @author Tony Perez <tonyp@zoosk.com>
 * @copyright Copyright (c) 2007-2013 Zoosk Inc.
 */

namespace Eventory\Objects\Event;

use Eventory\Objects\Event\Assets\EventAsset;

class Event
{
	public $id;
	public $eventKey;
	public $eventUrl;
	public $description;

	/** @var array EventAsset */
	public $assets = array();

	/** @var array string */
	protected $subUrls = array();

	public function getId()
	{
		return $this->id;
	}

	public function getKey()
	{
		return $this->eventKey;
	}

	public function addAssets($assets)
	{
		foreach ($assets as $asset){
			/** @var EventAsset $asset */
			$this->assets[$asset->key] = $asset;
		}
		ksort($this->assets);
	}

	public function addSubUrls(array $subUrls)
	{
		foreach ($subUrls as $subUrl){
			$this->subUrls[$subUrl] = $subUrl;
		}
		ksort($this->subUrls);
	}

	public function getSubUrls()
	{
		return array_keys($this->subUrls);
	}
}
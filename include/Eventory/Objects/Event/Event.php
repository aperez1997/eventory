<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>
 * @copyright Copyright (c) 2007-2013 Zoosk Inc.
 */

namespace Eventory\Objects\Event;

use Eventory\Objects\Event\Assets\EventAsset;
use Eventory\Objects\Performers\Performer;

class Event
{
	public $id;
	public $eventKey;
	public $eventUrl;
	public $description;

	/** @var array EventAsset */
	public $assets = array();

	/** @var array Performer */
	protected $performerIds = array();

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

	public function getAssets()
	{
		return $this->assets;
	}

	public function getSubUrls()
	{
		return array_keys($this->subUrls);
	}

	public function addPerformerId(Performer $performer)
	{
		$id = $performer->getId();
		$this->performerIds[$id] = $id;
	}

	public function getPerformerIds()
	{
		return $this->performerIds;
	}
}
<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>

 */

namespace Eventory\Objects\Event;

use Eventory\Objects\Event\Assets\EventAsset;
use Eventory\Objects\Performers\Performer;

class Event
{
	public static function CreateNew($url, $key)
	{
		$event = new Event();
		$event->eventUrl = $url;
		$event->eventKey = $key;
		return $event;
	}

	public $id;
	public $eventKey;
	public $eventUrl;
	public $description;
	protected $created;
	protected $updated;

	/** @var array EventAsset */
	public $assets = array();

	/** @var array Performer */
	protected $performerIds = array();

	/** @var array string */
	protected $subUrls = array();

	protected function __construct()
	{
		$this->created = time();
		$this->updated = time();
	}

	public function getId()
	{
		return $this->id;
	}

	public function getKey()
	{
		return $this->eventKey;
	}

	public function setKey($key){
		$this->eventKey = $key;
	}

	public function setUrl($url)
	{
		$this->eventUrl = $url;
	}

	public function addAssets($assets)
	{
		$this->updated = time();
		foreach ($assets as $asset){
			/** @var EventAsset $asset */
			$this->assets[$asset->key] = $asset;
		}
		ksort($this->assets);
	}

	public function addSubUrls(array $subUrls)
	{
		$this->updated = time();
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

	public function addPerformer(Performer $performer)
	{
		$this->updated = time();
		$id = $performer->getId();
		$this->performerIds[$id] = $performer->getName();
		$performer->addEventId($this->id);
	}

	public function getPerformerIds()
	{
		return $this->performerIds;
	}

	public function getCreated()
	{
		return $this->created;
	}

	public function setCreated($created)
	{
		$this->created = $created;
		$this->updated = $created;
	}

	public function getUpdated()
	{
		return $this->updated;
	}

	public function setUpdated($u)
	{
		$this->updated = $u;
	}
}

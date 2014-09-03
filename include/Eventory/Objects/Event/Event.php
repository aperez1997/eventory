<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>
 */

namespace Eventory\Objects\Event;

use Eventory\Objects\Event\Assets\EventAsset;
use Eventory\Objects\ObjectAbstract;
use Eventory\Objects\Performers\Performer;

class Event extends ObjectAbstract
{
	public static function CreateNew($url, $key)
	{
		$event = new Event();
		$event->eventUrl = $url;
		$event->eventKey = $key;
		return $event;
	}

	public static function CreateFromData($data)
	{
		$event = new Event();
		$event->loadFromData($data);
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

	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @deprecated
	 * @param $assets
	 */
	public function addAssets($assets)
	{
		if (!is_array($assets)){
			$assets = array($assets);
		}

		$this->updated = time();
		foreach ($assets as $asset){
			/** @var EventAsset $asset */
			$this->assets[$asset->key] = $asset;
		}
		ksort($this->assets);
	}

	/**
	 * @deprecated
	 * @param array $subUrls
	 */
	public function addSubUrls($subUrls)
	{
		if (!is_array($subUrls)){
			$subUrls = array($subUrls);
		}

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

	/**
	 * @deprecated
	 * @param Performer $performer
	 */
	public function addPerformer(Performer $performer)
	{
		if ($performer->isDeleted()){
			return;
		}

		$id = $performer->getId();
		$this->performerIds[$id] = $performer->getName();
		$performer->addEventId($this->id);
	}

	/**
	 * @deprecated
	 * @param Performer $performer
	 */
	public function removePerformer(Performer $performer)
	{
		$id = $performer->getId();
		unset($this->performerIds[$id]);
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

	public function setUpdated($u){
		$this->updated = $u;
	}

	public function getSortKey()
	{
		$assetFactor = count($this->assets) * 360;
		return $this->updated + $assetFactor;
	}

        public function getImageEventUrl(){
                return sprintf('/image.php?url=%s', $this->eventUrl);
        }
}

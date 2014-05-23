<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>

 */

namespace Eventory\Objects\Performers;

use Eventory\Objects\Event\Event;
use Eventory\Objects\ObjectAbstract;

class Performer extends ObjectAbstract
{
	const SORT_DEFAULT		= 'Default';
	const SORT_ALPHA 		= 'Alpha';
	const SORT_EVENTS		= 'EventCount';

	public static function CreateNew($name)
	{
		$perf = new Performer();
		$perf->name = $name;
		return $perf;
	}

	public static function CreateFromData($data)
	{
		$performer = new Performer();
		foreach ($performer as $k => $v){
			if (isset($data[$k])){
				$performer->$k = $data->$k;
			}
		}
		$performer->convertTinyIntToBool('deleted');
		$performer->convertTinyIntToBool('highlight');
		return $performer;
	}

	public $id;
	protected $name;
	protected $imageUrl;
	protected $siteUrls;
	protected $highlight;
	protected $eventIds;
	protected $updated;
	protected $deleted;

	protected function __construct()
	{
		$this->updated = time();
		$this->siteUrls = array();
		$this->eventIds = array();
	}

	public function getId()
	{
		return $this->id;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getImageUrl()
	{
		return $this->imageUrl;
	}

	public function setImageUrl($url)
	{
		$this->imageUrl = $url;
	}

	public function isHighlighted()
	{
		return $this->highlight;
	}

	public function setHighlight($flag)
	{
		$this->highlight = $flag;
	}

	public function getEventIds()
	{
		return $this->eventIds ? $this->eventIds : array();
	}

	public function getEventCount()
	{
		return count($this->eventIds);
	}

	public function addSiteUrl($url)
	{
		$this->siteUrls[$url] = $url;
	}

	public function getSiteUrls()
	{
		if (!is_array($this->siteUrls)){
			return array();
		}
		return $this->siteUrls;
	}

	/**
	 * @deprecated
	 * @param $eventId
	 */
	public function addEventId($eventId)
	{
		if ($eventId instanceof Event){
			$eventId = $eventId->getId();
		}
		$this->updated = time();
		$this->eventIds[$eventId] = $eventId;
	}

	/**
	 * @deprecated
	 * @param array $eventIds
	 */
	public function addEventIds(array $eventIds)
	{
		foreach ($eventIds as $eventId){
			$this->addEventId($eventId);
		}
	}

	/**
	 * @deprecated
	 * @param Event $event
	 */
	public function removeEvent(Event $event)
	{
		$id = $event->getId();
		unset($this->eventIds[$id]);
	}

	public function getUpdated()
	{
		return $this->updated;
	}

	public function getSortKey($sortType = null)
	{
		switch ($sortType){
			case self::SORT_ALPHA:
				$sortKey = strval($this->getName() . $this->getId());
				break;
			case self::SORT_EVENTS:
				$sortKey = (100000 - $this->getEventCount()) * 10000 + $this->getId();
				break;
			case self::SORT_DEFAULT:
			default:
				$bit = $this->highlight ? 'A' : 'Z';
				$sortKey = strval($bit . (2000000000 - $this->getUpdated()) . $this->getId());
				break;
		}

		return $sortKey;
	}

	public function isDeleted()
	{
		return $this->deleted == true;
	}

	public function setDeleted()
	{
		$this->deleted = true;
	}
}

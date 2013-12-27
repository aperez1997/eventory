<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>

 */

namespace Eventory\Objects\Performers;

use Eventory\Objects\Event\Event;

class Performer
{
	public static function CreateNew($name)
	{
		$perf = new Performer();
		$perf->name = $name;
		return $perf;
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

	public function addEventId($eventId)
	{
		$this->updated = time();
		$this->eventIds[$eventId] = $eventId;
	}

	public function addEventIds(array $eventIds)
	{
		foreach ($eventIds as $eventId){
			$this->addEventId($eventId);
		}
	}

	public function removeEvent(Event $event)
	{
		$id = $event->getId();
		unset($this->eventIds[$id]);
	}

	public function getUpdated()
	{
		return $this->updated;
	}

	public function getSortKey()
	{
		$bit = $this->highlight ? '1' : '0';
		return strval($bit . $this->updated . $this->id);
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

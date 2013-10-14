<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>

 */

namespace Eventory\Objects\Performers;

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

	public function getUpdated()
	{
		return $this->updated;
	}

	public function getSortKey()
	{
		$bit = $this->highlight ? '1' : '0';
		return intval($bit . $this->updated);
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

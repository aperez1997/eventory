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
	protected $eventIds;
	protected $updated;

	protected function __construct()
	{
		$this->updated = time();
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

	public function getEventIds()
	{
		return $this->eventIds;
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
}

<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>

 */

namespace Eventory\Objects;


class EventScrapeItem
{
	public $eventKey;
	public $eventUrl;
	public $eventThumb;
	public $eventDesc;

	public function __toString()
	{
		$parts = array(
			'k=' . $this->eventKey, 
			'u=' . $this->eventUrl,
		);
		if ($this->eventThumb){
			$parts[] = 't='.$this->eventThumb;
		}
		if ($this->eventDesc){
			$parts[] = 'd='.$this->eventDesc;
		}
		
		return join(',', $parts);
	}
}

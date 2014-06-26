<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>

 */

namespace Eventory\Objects\Event\Assets;

use Eventory\Objects\ObjectAbstract;

class EventAsset extends ObjectAbstract
{
	public $key;
	public $type;
	public $hostUrl;
	public $imageUrl;
	public $linkUrl;
	public $text;

	public static function CreateFromData($data)
	{
		$event = new EventAsset();
		$event->loadFromData($data);
		return $event;
	}
}
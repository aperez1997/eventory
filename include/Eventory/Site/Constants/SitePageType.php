<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>
 */

namespace Eventory\Site\Constants;

class SitePageType
{
	const DEFAULT_PAGE			= self::RECENT;
	const RECENT				= 'r';
	const EVENTS_UPDATED_SINCE  = 'eus';
	const EVENT					= 'e';
	const EVENT_EDIT			= 'ee';
	const EVENT_PERFORMER_ADD	= 'epa';
	const BROWSE_PERFORMERS		= 'bp';
	const BROWSE_PERFORMERS_NEW	= 'bpn';
	const PERFORMER				= 'p';
	const PERFORMER_EDIT		= 'pe';

	public static function GetAll()
	{
		return array(
			SitePageType::DEFAULT_PAGE,
			SitePageType::RECENT,
			SitePageType::EVENTS_UPDATED_SINCE,
			SitePageType::EVENT,
			SitePageType::EVENT_EDIT,
			SitePageType::EVENT_PERFORMER_ADD,
			SitePageType::BROWSE_PERFORMERS,
			SitePageType::BROWSE_PERFORMERS_NEW,
			SitePageType::PERFORMER,
			SitePageType::PERFORMER_EDIT,	
		);
	}
}
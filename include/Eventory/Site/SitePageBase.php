<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>

 */

namespace Eventory\Site;


use Eventory\Model\EventModel;
use Eventory\Model\PerformerModel;
use Eventory\Site\Browse\SiteBrowsePerformers;
use Eventory\Site\Constants\SitePageParams;
use Eventory\Site\Constants\SitePageType;
use Eventory\Storage\iStorageProvider;

abstract class SitePageBase
{
	/** @var iStorageProvider  */
	protected $store;
	/** @var EventModel  */
	protected $eventModel;
	/** @var PerformerModel */
	protected $performerModel;
	protected $postResult = null;
	protected $postResultMsg;

	public function __construct(iStorageProvider $store)
	{
		$this->store = $store;
		 // TODO: move this into a constructor dependency
		$this->eventModel = new EventModel($store);
		$this->performerModel = new PerformerModel($store);
	}

	public function getStorageProvider()
	{
		return $this->store;
	}

	public function getLinkRecentEvents($offset = null)
	{
		$paramPage = SitePageParams::PAGE;
		$pageViewRecent = SitePageType::RECENT;

		$url = "?{$paramPage}={$pageViewRecent}";

		if ($offset > 0){
			$paramOffset = SitePageParams::OFFSET;
			$url .= "&{$paramOffset}={$offset}";
		}
		return $url;
	}

	public function getLinkBrowsePerformers($sort = null)
	{
		$paramPage = SitePageParams::PAGE;
		$pageBrowsePerformers = SitePageType::BROWSE_PERFORMERS;
		$sortParam = SitePageParams::SORT;
		if (empty($sort)){
			$sort = SiteBrowsePerformers::SORT_DEFAULT;
		}
		$link = "?{$paramPage}={$pageBrowsePerformers}&{$sortParam}={$sort}";
		return $link;
	}

	public function getLinkPerformerView($performerId)
	{
		$paramPage = SitePageParams::PAGE;
		$pageViewPerf = SitePageType::PERFORMER;
		$paramPerformer = SitePageParams::PERFORMER_ID;
		return "?{$paramPage}={$pageViewPerf}&{$paramPerformer}={$performerId}";
	}

	public function getLinkEventPerformerAdd($eventId)
	{
		$paramPage = SitePageParams::PAGE;
		$pageType = SitePageType::EVENT_PERFORMER_ADD;
		$paramEvent = SitePageParams::EVENT_ID;
		return "?{$paramPage}={$pageType}&{$paramEvent}={$eventId}";
	}

	protected function renderContent($templatePath, $vars)
	{
		$page = $this;
		ob_start();
		require $templatePath;
		$content = ob_get_clean();
		return $content;
	}

	protected function renderMain($mainContent, $title)
	{
		$page = $this;
		ob_start();
		$navItems = $this->getNavItems();
		require __DIR__ .'/Templates/tmp_main.php';
		return ob_get_clean();
	}

	protected function getNavItems()
	{
		$navItems = array(
			array($this->getLinkRecentEvents(), 'Recent Events'),
			array($this->getLinkBrowsePerformers(), 'Performers', true),
		);
		return array_merge($navItems, $this->navItems);
	}

	protected $navItems = array();
	public function appendNavItems($items)
	{
		$this->navItems = $items;
	}

	protected function getTemplatesPath()
	{
		return __DIR__ . '/Templates/';
	}

	protected function isAdminPage()
	{
		return false;
	}

	protected function authenticate()
	{
		// TODO: properly
		return true;
	}

	public function isAdmin()
	{
		// TODO: properly
		return true;
	}

	/**
	 * @param array $params
	 */
	public function post(array $params){}

	/**
	 * @param $flag
	 * @param $msg
	 */
	protected function setPostStatus($flag, $msg)
	{
		$this->postResult = $flag;
		$this->postResultMsg = $msg;
	}

	/**
	 * @return bool
	 */
	public function hadPost()
	{
		return $this->postResult !== null;
	}

	public function wasPostSuccess()
	{
		return $this->postResult === true;
	}

	public function getPostResultMsg()
	{
		return $this->postResultMsg;
	}

	/**
	 * @param array $params
	 * @return string HTML
	 */
	abstract public function render(array $params);

	public function getTimeFormat()
	{
		return 'm-d-Y ga';
	}
}

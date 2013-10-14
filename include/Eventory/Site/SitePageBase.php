<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>

 */

namespace Eventory\Site;


use Eventory\Site\Constants\SitePageParams;
use Eventory\Site\Constants\SitePageType;
use Eventory\Storage\iStorageProvider;

abstract class SitePageBase
{
	protected $store;

	public function __construct(iStorageProvider $store)
	{
		$this->store = $store;
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

	public function getLinkBrowsePerformers()
	{
		$paramPage = SitePageParams::PAGE;
		$pageBrowsePerformers = SitePageType::BROWSE_PERFORMERS;
		return "?{$paramPage}={$pageBrowsePerformers}";
	}

	public function getLinkPerformerView($performerId)
	{
		$paramPage = SitePageParams::PAGE;
		$pageViewPerf = SitePageType::PERFORMER;
		$paramPerformer = SitePageParams::PERFORMER_ID;
		return "?{$paramPage}={$pageViewPerf}&{$paramPerformer}={$performerId}";
	}

	protected function renderContent($templatePath, $vars)
	{
		$page = $this;
		ob_start();
		require $templatePath;
		$content = ob_get_clean();
		return $content;
	}

	protected function renderMain($mainContent)
	{
		$page = $this;
		ob_start();
		require __DIR__ .'/Templates/tmp_main.php';
		return ob_get_clean();
	}

	protected function getTemplatesPath()
	{
		return __DIR__ . '/Templates/';
	}

	/**
	 * @param array $params
	 * @return string HTML
	 */
	abstract public function render(array $params);
}
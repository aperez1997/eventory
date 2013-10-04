<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>
 * @copyright Copyright (c) 2007-2013 Zoosk Inc.
 */

namespace Eventory\Site;


use Eventory\Storage\iStorageProvider;

abstract class SitePageBase
{
	protected $store;

	public function __construct(iStorageProvider $store)
	{
		$this->store = $store;
	}

	protected function renderContent($templatePath)
	{
		ob_start();
		include $templatePath;
		$content = ob_get_clean();
		return $content;
	}

	protected function renderMain($mainContent)
	{
		global $content;
		$content = $mainContent;
		ob_start();
		include __DIR__ .'/Templates/tmp_main.php';
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
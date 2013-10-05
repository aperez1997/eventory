<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>
 */

use Eventory\Site\SitePageIndex;

require_once __DIR__ .'/../bootstrap.php';

$page = new SitePageIndex(getStoreProvider());
echo $page->render($_GET);
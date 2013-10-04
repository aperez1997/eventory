<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>
 * @copyright Copyright (c) 2007-2013 Zoosk Inc.
 */

require_once __DIR__ .'/../bootstrap.php';

use Eventory\Site\Browse\SiteBrowseRecentEvents;

$page = new SiteBrowseRecentEvents(getStoreProvider());
echo $page->render($_GET);
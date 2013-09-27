<?php
/**
 * @author Tony Perez <tonyp@zoosk.com>
 * @copyright Copyright (c) 2007-2013 Zoosk Inc.
 */

use Eventory\Examples\ExampleSiteScraperFactory;
use Eventory\Examples\Slashdot\SlashdotListSiteScraper;

require_once __DIR__ . '/../../../../bootstrap.php';

$siteScraperFactory = new ExampleSiteScraperFactory();
$siteListScraper = new SlashdotListSiteScraper();
$events = $siteListScraper->scrapeFromWeb($siteScraperFactory);
print_r($events);
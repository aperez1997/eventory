<?php
/**
 * @author Tony Perez <tonyp@zoosk.com>
 * @copyright Copyright (c) 2007-2013 Zoosk Inc.
 */

use Eventory\Examples\ExampleSiteScraperFactory;
use Eventory\Examples\Slashdot\SlashdotListSiteScraper;
use Eventory\Storage\File\StorageProviderSerialized;

require_once __DIR__ . '/../../../../bootstrap.php';

$fileName = __DIR__ .'/slashdot.data';
$storeProvider = new StorageProviderSerialized($fileName);
$siteScraperFactory = new ExampleSiteScraperFactory($storeProvider);
$siteListScraper = new SlashdotListSiteScraper($siteScraperFactory);
printf("Scraping slashdot\n");
$events = $siteListScraper->scrapeFromWeb();
$storeProvider->saveEvents($events);
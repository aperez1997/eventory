<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>
 * @copyright Copyright (c) 2007-2013 Zoosk Inc.
 */

use Eventory\Examples\ExampleSiteScraperFactory;
use Eventory\Examples\Slashdot\SlashdotListSiteScraper;
use Eventory\Storage\File\StorageProviderSerialized;

require_once __DIR__ . '/bootstrap.php';

$storeProvider = getStoreProvider();
$siteScraperFactory = new ExampleSiteScraperFactory($storeProvider);
$siteListScraper = new SlashdotListSiteScraper($siteScraperFactory);
printf("Scraping slashdot\n");
$events = $siteListScraper->scrapeFromWeb();
$storeProvider->saveEvents($events);
<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>

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
printf("writing\n");
$storeProvider->saveEvents($events);
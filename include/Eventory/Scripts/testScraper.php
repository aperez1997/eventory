<?php
/**
 * @author Tony Perez <tonyp@zoosk.com>
 * @copyright Copyright (c) 2007-2018 Zoosk Inc.
 */

use Eventory\Examples\ExampleSiteScraperFactory;
use Eventory\Examples\Slashdot\SlashdotListSiteScraper;
use Eventory\Storage\iStorageProvider;

require_once __DIR__ . '/../bootstrap.php';


function getScraper(iStorageProvider $storageProvider)
{
	$siteScraperFactory = new ExampleSiteScraperFactory($storageProvider);
	$siteListScraper = new SlashdotListSiteScraper($siteScraperFactory);
	return $siteListScraper;
}	

$storageProvider = getStoreProvider();
$siteListScraper = getScraper($storageProvider);

printf("Scrape items:\n");
$scrapeItems = $siteListScraper->scrapeFromWebIntoScrapeItems();
foreach ($scrapeItems as $scrapeItem){
	$string = $scrapeItem->__toString();
	$string = str_replace(',', "\n  ", $string);
	printf("%s\n", $string);
}

printf("\nEvents:\n");
$events = $siteListScraper->scrapeFromWeb();
foreach ($events as $event){
	printf("%s\n", $event);	
}

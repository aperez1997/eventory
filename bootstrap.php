<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>
 */

$projectRoot = dirname(__FILE__);

// Auto-loading
$vendorPath = $projectRoot . '/vendor/';
require_once $vendorPath . '/SplClassLoader.php';
$eventoryIncludePath = $projectRoot . '/include/';
$splClassLoader = new SplClassLoader('Eventory', $eventoryIncludePath);
$splClassLoader->register();

require_once $vendorPath . '/simple_html_dom.php';
<?php
/**
 * @author Tony Perez <tonyp@zoosk.com>
 * @copyright Copyright (c) 2007-2013 Zoosk Inc.
 */

$projectRoot = dirname(__FILE__);

// Auto-loading
$vendorPath = $projectRoot . '/vendor/';
require_once $vendorPath . '/SplClassLoader.php';
$eventoryIncludePath = $projectRoot . '/include/';
$splClassLoader = new SplClassLoader('Eventory', $eventoryIncludePath);
$splClassLoader->register();

require_once $vendorPath . '/simple_html_dom.php';
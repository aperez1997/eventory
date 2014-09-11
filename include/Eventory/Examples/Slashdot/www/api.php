<?php
use Eventory\Site\SiteApiProcessor;

require_once __DIR__ .'/../bootstrap.php';

$page = new SiteApiProcessor(getStoreProvider());
$page->handle();

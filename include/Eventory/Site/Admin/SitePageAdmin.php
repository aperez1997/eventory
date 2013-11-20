<?php

namespace Eventory\Site\Admin;

use Eventory\Site\SitePageBase;

abstract class SitePageAdmin extends SitePageBase
{
	protected function isAdminPage(){ return true; }
} 
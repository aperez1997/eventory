<?php
/**
 * @author Tony Perez <tonyp@zoosk.com>
 * @copyright Copyright (c) 2007-2014 Zoosk Inc.
 */

namespace Eventory\Site;

use Eventory\Model\EventModel;
use Eventory\Site\Admin\SiteApiAdmin;
use Eventory\Storage\iStorageProvider;

class SiteApiProcessor 
{
	protected $store;
	protected $router;
	protected $adminApi;
	
	public function __construct(iStorageProvider $store){
		$this->store = $store;
		$model = new EventModel($this->store);
		$this->adminApi = new SiteApiAdmin($store, $model);
	}
	
	public function handle()
	{
		$router = $this->getRouter();	
		try {
			$path = $_REQUEST['path'];
			if ($path[0] != '/'){ $path = '/' . $path; }
			$router->route($path);
		} catch (\Zaphpa_InvalidPathException $ex) {      
			header("Content-Type: application/json;", TRUE, 404);
			$out = array("error" => "api not found");        
			die(json_encode($out));
		} catch (\Exception $ex){
			header("Content-Type: application/json;", TRUE, 500);
			$out = array("error" => sprintf("Server error %s", $ex->getMessage()));
			error_log(sprintf("Exception (%s) %s\n", get_class($ex), $ex->__toString()));
			die(json_encode($out));			
		}
	}
	
	protected function getRouter()
	{
		if (!isset($this->router)){
			$router = new \Zaphpa_Router();
			$router->addRoute(array(
				'path' => '/admin/event/{event_id}/performer/{performer_id}/remove',
				'handlers' => array(
					'event_id' => \Zaphpa_Constants::PATTERN_DIGIT,
					'performer_id' => \Zaphpa_Constants::PATTERN_DIGIT,
				),
				'post' => array($this->adminApi, 'removeEventPerformer'),
			));
			$router->addRoute(array(
                'path' => '/admin/performer/{performer_id}/delete',
                'handlers' => array(
                    'performer_id' => \Zaphpa_Constants::PATTERN_DIGIT,
                ),
                'post' => array($this->adminApi, 'deletePerformer'),
            ));
			$router->addRoute(array(
                'path' => '/admin/performer/{performer_id_dupe}/duplicate/{performer_id_real}',
                'handlers' => array(
                    'performer_id_dupe' => \Zaphpa_Constants::PATTERN_DIGIT,
	                'performer_id_real' => \Zaphpa_Constants::PATTERN_DIGIT,
                ),
                'post' => array($this->adminApi, 'markDuplicate'),
            ));
			$this->router = $router;
		}
		return $this->router;
	}
}

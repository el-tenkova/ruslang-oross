<?php

namespace Admin;

use Admin\Model\Changes;
use Admin\Model\ChangesTable;
use Admin\Model\Dics;
use Admin\Model\DicsTable;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\ModuleManager;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Db\Adapter\Adapter;

class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                //    'Admin' => __DIR__ . '/src/' . 'Admin',
                ),
            ),
        );
    }
    
	public function init(ModuleManager $manager)
	{
    	$events = $manager->getEventManager();
   	 	$sharedEvents = $events->getSharedManager();
    	$sharedEvents->attach(__NAMESPACE__, 'dispatch', function($e) {
        	$controller = $e->getTarget();
        	if (get_class($controller) == 'Admin\Controller\AdminRosanController')         {
	        	error_log(sprintf("controller = %s", get_class($controller)));
            	$controller->layout('layout/admin');
        	}
    	}, 100);
	}   
    public function getServiceConfig()
    {
        return [
            'factories' => [
                'Admin\Model\ChangesTable' =>  function($sm) {
                    error_log("create Changes table");
                    $tableGateway = $sm->get('ChangesTableGateway');
                    $table = new ChangesTable($tableGateway);
                    return $table;
                },
                'ChangesTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Changes());
                    return new TableGateway(['c' => 'changes'], $dbAdapter, null, $resultSetPrototype);
                },
                'Admin\Model\DicsTable' =>  function($sm) {
                    error_log("create Dics table");
                    $tableGateway = $sm->get('DicsTableGateway');
                    $table = new DicsTable($tableGateway);
                    return $table;
                },
                'DicsTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Dics());
                    return new TableGateway(['d' => 'dics'], $dbAdapter, null, $resultSetPrototype);
                },
                
                ],
            ]; 
	 }
}

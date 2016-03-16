<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Contents;

use Contents\Model\ContentsTables;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Navigation\Service\DefaultNavigationFactory;
use Zend\Navigation\Navigation;

class ContentsNavigation extends DefaultNavigationFactory implements FactoryInterface 
{
    
/*    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sl = $serviceLocator;
        $this->captureTo('navigation');
        return $this;
    } */

/*    public function createService(ServiceLocatorInterface $serviceLocator)
    {
//        error_log("ContentsNavigation : createService");
//        $pages = $this->getPages($serviceLocator);
 //       return new Navigation($pages);
    } */

    public function getPages(ServiceLocatorInterface $serviceLocator)
    {
        $navigation = array();
        if (null === $this->pages) {
			error_log("getPages for navigation");

            $parts = $serviceLocator->get('Contents\Model\ContentsTables')->getContents($serviceLocator);
    
            if ($parts)
            {
                foreach ($parts as $key => $part)
                { 
                    //      error_log($key);
                    $tiles = array();
                    foreach ($part['tiles'] as $keytile => $tile)
                    {
                        //    error_log($key);
                        $paras = array();
                        foreach ($tile['paras'] as $para)
                        {
                            // error_log($para['name']);
                            $paras[] = array(
                                        'label' => $para['name'],
                                        'route' => 'paragraph', 
                                        'action' => 'view', 
                                        'params' => array('id' => $para['id'], 'examples' => $para['examples']),
    //                                        'uri' => "1", //$para['id'], //$serviceLocator->url('paragraph', array('action' => 'view', 'id' => $para['id'])),
                                        ); 
                        } 
                        $tiles[] = array(
                            'label' => $keytile,
                            'uri'   => $tile['id'],
                            'pages' => $paras,
                        ); // props
                    }
                    $navigation[] = array(
                        'label' => $key,
                        'uri'   => $part['id'],
                        'pages' => $tiles,
                    ); // props
                }
            }
        }
        $mvcEvent = $serviceLocator->get('Application')->getMvcEvent();

        $routeMatch = $mvcEvent->getRouteMatch();
        $router     = $mvcEvent->getRouter();
        $pages      = $navigation;


        $this->pages = $this->injectComponents($pages, $routeMatch, $router);

        return $this->pages;
    }
}
?>
 
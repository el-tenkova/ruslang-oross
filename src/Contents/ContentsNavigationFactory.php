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
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\View\Model\ViewModel;

class ContentsNavigationFactory implements FactoryInterface
{
    protected $data;
    //public $leftside;
/*    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {error_log("create leftside1");
    error_log($requestedName);
        // this abstract factory only knows about 'foo' and 'bar'
        return $requestedName === 'foo' || $requestedName === 'bar';
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        error_log("create leftside2");
        $service = new \stdClass();

        $service->name = $requestedName;

        return $service;
    } */
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
       /* if ($this->data == null)
        {
            error_log("create leftside");
            // $sm = $this->getServiceLocator();
            $contentsTables = $serviceLocator->get('Contents\Model\ContentsTables');
            $this->data = $contentsTables->getContents();
        }
        return $this->data; */
//        print_r($ret);
        
//        $leftside = new ViewModel(array('leftside' => "Hello from leftside"));
  //      $leftside->setTemplate('contents/left');
      //  return $leftside;
//$this->leftside = array('navigation' => "Hello from leftside");
        //return $this->leftside;
        error_log("create navigation");
        
        $leftside = new ViewModel(array('leftside' => "This is left side", 'contents' => ContentsTables::getContents($serviceLocator);
        $leftside->setTemplate('contents/left');
        return $leftside;
//        return $serviceLocator->get('ContentsNavigation');
//        $navigation = new ContentsNavigation();
  //      return $navigation->createService($serviceLocator);
    } 
}

?>
 
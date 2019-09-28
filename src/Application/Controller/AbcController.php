<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Interop\Container\ContainerInterface;

use Zend\Mvc\MvcEvent;

use Contents\Model\AbcTables;
use Contents\Model\ArticleTables;

class AbcController extends AbstractActionController
{	
    public function viewAction()
    {
    	error_log("AbcController - view");

    	$ret = AbcTables::getLetter($this->getServiceLocator(), 'a');
    	$abc = AbcTables::getABC();
        return new ViewModel(['parts' => $ret,
        					  'letter' => 'a',
							  'abc' => $abc,
                              ]);
    }

    public function letterAction()
    {
    	error_log("AbcController - letter");
    	$letter = $this->params('id');
    	
    	$ret = AbcTables::getLetter($this->getServiceLocator(), $letter);
    	$abc = AbcTables::getABC();
        return new ViewModel(['parts' => $ret,
        					  'letter' => $letter,
                              'abc' => $abc,
                              ]);
    }
    
    public function partAction()
    {
    	error_log("AbcController - part");
    	$letter = $this->params('id');
    	
    	$ret = AbcTables::getLetter( $this->getServiceLocator(), $letter);
    	$abc = AbcTables::getABC();
		
        $start = $this->params()->fromQuery('start', 0);
		$end = $this->params()->fromQuery('end', 0);
		
		$prev = AbcTables::getPrev($this->getServiceLocator(), $letter, $start);
		$next = AbcTables::getNext($this->getServiceLocator(), $letter, $end);

		$articles = array();
		
		if ($start != 0 && $end != 0)
			$articles = ArticleTables::getInterval($this->getServiceLocator(), $start, $end);    	
        return new ViewModel(['parts' => $ret,
        					  'letter' => $letter,
        					  'articles' => $articles,
                              'abc' => $abc,
                              'prev' => $prev,
                              'next' => $next,
                              ]);
    }
}

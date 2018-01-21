<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Contents\Model\ContentsTables;
use Contents\Model\ParagraphTables;
use Contents\Model\RuleTables;
use Contents\Model\OrthogrTables;
use Contents\Model\FormulaTables;
use Contents\Model\WordTables;
//use Contents\Model\HistoricTables;
use Contents\Model\PredislTable;
use Contents\Model\StateTable;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Zend\Mvc\MvcEvent;

use Application\Form\SearchForm;
use Application\Form\DownloadForm;

use Zend\Http\Headers;
use Zend\Http\Response\Stream;
use Zend\Http\Client;


class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        error_log("IndexController : indexAction");
        $state = StateTable::getState($this->getServiceLocator());
        $s = StateTable::Ok;
        if (count($state) > 0 && $state['state'] == StateTable::UnderReconstruction)
            $s = 2;
        $view = new ViewModel();
    	$predisl = PredislTable::getPredisl($this->getServiceLocator());
		return new ViewModel(array('predisl' => $predisl,
		                                              'state' => $s));    	
    }
}

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
use Contents\Model\HistoricTables;
use Contents\Model\ArticleTables;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Zend\Mvc\MvcEvent;

use Application\Form\WordForm;
use Application\Form\HistoricForm;
use Application\Form\DownloadForm;

use Zend\Http\Headers;
use Zend\Http\Response\Stream;
use Zend\Http\Client;

use Zend\Paginator;

class SearchController extends AbstractActionController
{
	const FOR_PAGE_COUNT = 50;
    protected $paginator;

    public function __construct()
    {
    	//error_log("SearchController __construct");
        $this->paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter(array()));
        $this->paginator->setItemCountPerPage(self::FOR_PAGE_COUNT);
    }
    
    public function indexAction()
    {
        error_log("SearchController : indexAction");
        $view = new ViewModel();
       	$wordform = new WordForm();
       	$view->setVariable('wordform', $wordform); 
       	$histform = new HistoricForm(null, HistoricTables::getAllHistoric($this->getServiceLocator()));
       	$view->setVariable('histform', $histform); 

/*        $request = $this->getRequest();

		// set the current page to what has been passed in query string, or to 1 if none set
		$this->paginator->setCacheEnabled(true);
		$this->paginator->getItemsByPage(3);
		$this->paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
		error_log(sprintf("pagination page = %d", (int) $this->params()->fromQuery('page', 1)));
        
        if ($request->isPost()) {
        	//error_log("request is Post");
			//$paginator = new \Zend\Paginator\Paginator(NullFill);        	
			//$paginator->setCurrentPageNumber($this->params()->fromRoute('page'));
			
			$data = $request->getPost();
			$arts = array();
			$word = "";
			if (isset($data['submit']) || isset($data['submithist'])) {
				// arts is of type Zend/Paginator
				if (isset($data['submit']) && isset($data['word']) && strlen($data['word']) > 0) {
					$word = $data['word']; 
					$arts = WordTables::getArticles($this->getServiceLocator(), $word);
				}
				else if (isset($data['submithist']) && isset($data['historic']) && $data['historic'] != 0) {
					$word = HistoricTables::getName($this->getServiceLocator(), $data['historic']);
					$arts = HistoricTables::getArticles($this->getServiceLocator(), $data['historic']);
				}
				
				if (count($arts) > 0) {
					$a = count($arts);
					while ($a > 10) {
						$a = $a % 10;
					}
					if ($a == 1)
						$title = sprintf('По запросу "%s" найдена 1 статья', $word);
					else if ($a == 2 || $a == 3 || $a == 4)
						$title = sprintf('По запросу "%s" найдено %d статьи', $word, count($arts));
					else
						$title = sprintf('По запросу "%s" найдено %d статей', $word, count($arts));
				}
				else {
					$title = sprintf('По запросу "%s" ничего не найдено', $word, count($arts));
				}				
				$this->paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($arts));
/*$cache   = \Zend\Cache\StorageFactory::factory(array(
     'adapter' => array(
         'name' => 'filesystem',
         'options' => array(
             'cache_dir' => 'tmp/cache',
             'ttl' => 3600,
         ),
     ),
     'plugins' => array(
         // Don't throw exceptions on cache errors
         'exception_handler' => array(
             'throw_exceptions' => false
         ),
         'serializer' => array(
             'serializer' => 'Zend\Serializer\Adapter\PhpSerialize',
             'options' => array(),
        ),
     )
 )); 
 Zend\Paginator\Paginator::setCache($cache);*/
//				$cache = StorageFactory::factory(array('adapter' => $arts,
//												       'plugins' => array('exception_handler' => array('throw_exceptions' => false))));
//				Paginator::setCache($cache);
/*				$articles = new ViewModel(array('articles' => $this->paginator, 'title' => $title, 'pageCount' => count($arts)));
						
				$articles->setTemplate('contents/articles');
				$view->addChild($articles, 'articles'); */
/*			}
        }
		$articles = new ViewModel(array('articles' => $this->paginator));//, 'title' => $title, 'pageCount' => count($arts)));
						
		$articles->setTemplate('contents/articles');
		$view->addChild($articles, 'articles'); */
		return $view;       	
    }
    
    public function wordAction()
    {
        error_log("SearchController : wordAction");
        $view = new ViewModel();
       	$wordform = new WordForm();
       	$view->setVariable('wordform', $wordform); 
        $request = $this->getRequest();

		// set the current page to what has been passed in query string, or to 1 if none set
		$this->paginator->setCacheEnabled(true);
		$this->paginator->getItemsByPage(3);
		$this->paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
		error_log(sprintf("pagination page = %d", (int) $this->params()->fromQuery('page', 1)));
        
        if ($request->isPost()) {
        	//error_log("request is Post");
			//$paginator = new \Zend\Paginator\Paginator(NullFill);        	
			//$paginator->setCurrentPageNumber($this->params()->fromRoute('page'));
			
			$data = $request->getPost();
			$arts = array();
			$word = "";
			if (isset($data['submit']) || isset($data['submithist'])) {
				// arts is of type Zend/Paginator
				if (isset($data['submit']) && isset($data['word']) && strlen($data['word']) > 0) {
					$word = $data['word']; 
					$arts = WordTables::getArticles($this->getServiceLocator(), $word);
				}
				else if (isset($data['submithist']) && isset($data['historic']) && $data['historic'] != 0) {
					$word = HistoricTables::getName($this->getServiceLocator(), $data['historic']);
					$arts = HistoricTables::getArticles($this->getServiceLocator(), $data['historic']);
				}
				
				if (count($arts) > 0) {
					$a = count($arts);
					while ($a > 10) {
						$a = $a % 10;
					}
					if ($a == 1)
						$title = sprintf('По запросу "%s" найдена 1 статья', $word);
					else if ($a == 2 || $a == 3 || $a == 4)
						$title = sprintf('По запросу "%s" найдено %d статьи', $word, count($arts));
					else
						$title = sprintf('По запросу "%s" найдено %d статей', $word, count($arts));
				}
				else {
					$title = sprintf('По запросу "%s" ничего не найдено', $word, count($arts));
				}				
				$this->paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($arts));
/*$cache   = \Zend\Cache\StorageFactory::factory(array(
     'adapter' => array(
         'name' => 'filesystem',
         'options' => array(
             'cache_dir' => 'tmp/cache',
             'ttl' => 3600,
         ),
     ),
     'plugins' => array(
         // Don't throw exceptions on cache errors
         'exception_handler' => array(
             'throw_exceptions' => false
         ),
         'serializer' => array(
             'serializer' => 'Zend\Serializer\Adapter\PhpSerialize',
             'options' => array(),
        ),
     )
 )); 
 Zend\Paginator\Paginator::setCache($cache);*/
//				$cache = StorageFactory::factory(array('adapter' => $arts,
//												       'plugins' => array('exception_handler' => array('throw_exceptions' => false))));
//				Paginator::setCache($cache);
/*				$articles = new ViewModel(array('articles' => $this->paginator, 'title' => $title, 'pageCount' => count($arts)));
						
				$articles->setTemplate('contents/articles');
				$view->addChild($articles, 'articles'); */
			}
        }
		$articles = new ViewModel(array('articles' => $this->paginator));//, 'title' => $title, 'pageCount' => count($arts)));
						
		$articles->setTemplate('contents/articles');
		$view->addChild($articles, 'articles');
		return $view;       	
    }

    public function historicAction()
    {
        error_log("SearchController : historicAction");
        $view = new ViewModel();
       	$wordform = new WordForm();
       	$view->setVariable('wordform', $wordform); 
       	$histform = new HistoricForm(null, HistoricTables::getAllHistoric($this->getServiceLocator()));
       	$view->setVariable('histform', $histform); 
       	
        $request = $this->getRequest();

        $title = "";
		$historic = $this->params()->fromQuery('id', 0);
		$page = $this->params()->fromQuery('page', 1);
		$arts = array();
		$show = array();
		$found = 0;
        
        if ($request->isPost()) {
			$data = $request->getPost();
			if (isset($data['submithist'])) {
				if (isset($data['historic']) && $data['historic'] != 0) {
					$historic = $data['historic'];
				}
			}
        }
        if ($historic != 0) {
			$word = HistoricTables::getName($this->getServiceLocator(), $historic);
			$arts = HistoricTables::getArticles($this->getServiceLocator(), $historic, true, self::FOR_PAGE_COUNT, $page);
			$found = $arts['count'];	
			if ($found > 0) {
				$a = $found;
				while ($a > 100) {
					$a = $a % 10;
				}
				if ($a > 10 && $a < 20) 
					$title = sprintf('По запросу "%s" найдено %d статей', $word, $found);
				else {
					$a = $a % 10;
					if ($a == 1)
						$title = sprintf('По запросу "%s" найдена %d статья', $word, $found);
					else if ($a == 2 || $a == 3 || $a == 4)
						$title = sprintf('По запросу "%s" найдено %d статьи', $word, $found);
					else
						$title = sprintf('По запросу "%s" найдено %d статей', $word, $found);
				}
				$this->paginator = $arts['paginator'];
				$show = $arts['show'];
			}
			else {
				$title = sprintf('По запросу "%s" ничего не найдено', $word, count($arts));
			}				
        }
		$articles = new ViewModel(array('articles' => $show, 
										'paginator' => $this->paginator, 
										'route' => 'search', 
										'action' => 'historic', 
										'title' => $title, 
										'id' => $historic, 
										'pageCount' => count($this->paginator)));
						
		$articles->setTemplate('contents/articles');
		$view->addChild($articles, 'articles');
		return $view;       	
    }
    
}

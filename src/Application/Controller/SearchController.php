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
use Contents\Model\ArticleTables;
use Contents\Model\MistakeTables;

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
    protected $art_paginator;
    protected $rule_paginator;

    public function __construct()
    {
    	//error_log("SearchController __construct");
        $this->art_paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter(array()));
        $this->art_paginator->setItemCountPerPage(self::FOR_PAGE_COUNT);
        $this->rule_paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter(array()));
        $this->rule_paginator->setItemCountPerPage(self::FOR_PAGE_COUNT);
    }
    
    public static function getArticles($sm, $query, $title_check, $text_check, $yo, $tut_check, $page)    
    {
		$arts = array();
		$show = array();
		$found = 0;
    	
		$arts = WordTables::getArticles($sm, $query, ($title_check + $text_check), $yo, true, self::FOR_PAGE_COUNT, $page);
		$found = isset($arts['count']) ? $arts['count'] : 0;
		$mist = false;
		if ($found == 0) {
			$arts = MistakeTables::getRelArticles($sm, $query, ($title_check + $text_check), $yo, true, self::FOR_PAGE_COUNT, $page);
			$found = isset($arts['count']) ? $arts['count'] : 0;
			$mist = true;
		}
		if ($found > 0) {
			$a = $found;
			while ($a > 100) {
				$a = $a % 10;
			}
			if ($mist === false) {
				if ($a > 10 && $a < 20) 
					$title = sprintf('По запросу "%s" найдено %d статей', $query, $found);
				else {
					$a = $a % 10;
					if ($a == 1)
						$title = sprintf('По запросу "%s" найдена %d статья', $query, $found);
					else if ($a == 2 || $a == 3 || $a == 4)
						$title = sprintf('По запросу "%s" найдено %d статьи', $query, $found);
					else
						$title = sprintf('По запросу "%s" найдено %d статей', $query, $found);
				}
			}
			else {
				$title = sprintf('По запросу "%s" в словаре найдены похожие статьи:', $query, $found);
			}
			//$this->art_paginator = $arts['paginator'];
			$show = $arts['show'];
		}
		else {
			$title = sprintf('По запросу "%s" в словаре ничего не найдено', $query);
		}
		$articles = new ViewModel(array('articles' => $show, 
										'paginator' => $arts['paginator'], //$this->art_paginator, 
										'route' => 'search', 
										'action' => 'word', 
										'pag_part' => 'contents/paginator1.phtml',
										'title' => $title, 
										'query' => $query,
										'title_check' => $title_check,
										'text_check' => $text_check,
										'tut_check' => $tut_check,
										'yo' => $yo,
										'pageCount' => count($arts['paginator']))); //$this->art_paginator)));
						
		$articles->setTemplate('contents/articles');
		return $articles;    	
    }
    
    public function getRules($query, $title_check, $text_check, $tut_check, $page)
    {
		$rls = array();
		$show = array();
		$found = 0;
    	
		$rls = WordTables::getTutorial($this->getServiceLocator(), $query, $tut_check, 0, true, self::FOR_PAGE_COUNT, $page);
		$found = isset($rls['count']) ? $rls['count'] : 0;	
		if ($found == 0) {
			$title = sprintf('По запросу "%s" в справочнике ничего не найдено', $query);
		}
		else {
			$title = sprintf('По запросу "%s" найдены разделы справочника:', $query);
			$this->rule_paginator = $rls['paginator'];
			$show = $rls['show'];
		}
		$rules = new ViewModel(array('rules' => $show, 
									 'paginator' => $this->rule_paginator, 
									 'route' => 'search', 
									 'action' => 'word', 
									 'pag_part' => 'contents/paginator1.phtml',
									 'title' => $title, 
									 'query' => $query,
									 'title_check' => $title_check,
									 'text_check' => $text_check,
									 'tut_check' => $tut_check,
									 'pageCount' => count($this->rule_paginator)));
		
		$rules->setTemplate('contents/rules');
    	return $rules;
    }
    
    public function indexAction()
    {
        error_log("SearchController : indexAction");
        $view = new ViewModel();
       	$wordform = new WordForm();
       	$view->setVariable('wordform', $wordform); 
   //    	$histform = new HistoricForm(null, HistoricTables::getAllHistoric($this->getServiceLocator()));
   //    	$view->setVariable('histform', $histform); 
		return $view;       	
    }
    
    public static function processRequest($sm, $request, $params, $view)
    {
        $title = "";
		$query = urldecode($params->fromQuery('query', "-"));
		$page = $params->fromQuery('page', 1);
		$found = 0;
		
		$title_check = $params->fromQuery('title_check', 0);
		$text_check = $params->fromQuery('text_check', 0);
		$yo = $params->fromQuery('yo', 0);
		
		$wordform = $view->wordform;
  //      error_log(sprintf("search_part = %s", $search_part));
        if ($request->isPost()) {
			$data = $request->getPost();
			if (isset($data['submit'])) {
				if (isset($data['word']) && strlen($data['word']) > 0) {
					$query = $data['word'];
				}
			}
			if (isset($data['title_check']))
				$title_check = $data['title_check'] == 'yes' ? 1 : 0;
			if (isset($data['text_check']))
				$text_check = $data['text_check'] == 'yes' ? 2 : 0;
			if (isset($data['yo']))
				$yo = $data['yo'] == 'yes' ? 1 : 0;
        }
        if (strlen($query) > 0) {
			$wordform->get('word')->setAttribute('value', $query);
        	if ($title_check != 0)
				$wordform->get('title_check')->setAttribute('value', 'yes');
			else if ($title_check == 0 || !isset($data['title_check']) || $data['title_check'] == "no") {
				//$title_check = 0;
				$wordform->get('title_check')->setAttribute('value', 'no');
			}
			if ($text_check != 0)
				$wordform->get('text_check')->setAttribute('value', 'yes');
			else if ($text_check == 0 || !isset($data['text_check']) || $data['text_check'] == "no") {
				//$text_check = 0;
				$wordform->get('text_check')->setAttribute('value', 'no');
			}
			if ($yo != 0)
				$wordform->get('yo')->setAttribute('value', 'yes');
			else if ($yo == 0 || !isset($data['yo']) || $data['yo'] == "no") {
				//$yo = 0;
				if ($wordform->has('yo'))
					$wordform->get('yo')->setAttribute('value', 'no');
			}
			//print_r($data);
			$articles = SearchController::getArticles($sm, $query, $title_check, $text_check, $yo, 0, $page);

        }
		return $articles;    	
    }
    
    public function wordAction()
    {
        error_log("SearchController : wordAction");
        $view = new ViewModel();
       	$wordform = new WordForm();
       	$view->setVariable('wordform', $wordform); 
  //     	$histform = new HistoricForm(null, HistoricTables::getAllHistoric($this->getServiceLocator()));
  //     	$view->setVariable('histform', $histform); 
       	
        $request = $this->getRequest();

        $title = "";
		$query = urldecode($this->params()->fromQuery('query', '-'));
		error_log(sprintf("after decode: %s", $query));
		$tab = $this->params()->fromQuery('tab', 'dic');
		$page = $this->params()->fromQuery('page', 1);
		$found = 0;
		
		$title_check = $this->params()->fromQuery('title_check', 0);
		$text_check = $this->params()->fromQuery('text_check', 0);
		$tut_check = $this->params()->fromQuery('tut_check', 0);
		$yo = $this->params()->fromQuery('yo', 0);
		
  //      error_log(sprintf("search_part = %s", $search_part));
        if ($request->isPost()) {
			$data = $request->getPost();
			if (isset($data['submit'])) {
				if (isset($data['word']) && strlen($data['word']) > 0) {
					$query = $data['word'];
				}
			}
			//print_r($data);
			if (isset($data['title_check']))
				$title_check = $data['title_check'] == 'yes' ? 1 : 0;
			if (isset($data['text_check']))
				$text_check = $data['text_check'] == 'yes' ? 2 : 0;
			if (isset($data['tut_check']))
				$tut_check = $data['tut_check'] == 'yes' ? 1 : 0;
			if (isset($data['yo']))
				$yo = $data['yo'] == 'yes' ? 1 : 0;
        }
        if (strlen($query) > 0) {
			$wordform->get('word')->setAttribute('value', $query);
        	if ($title_check != 0)
				$wordform->get('title_check')->setAttribute('value', 'yes');
			else if ($title_check == 0 || !isset($data['title_check']) || $data['title_check'] == "no") {
				//$title_check = 0;
				$wordform->get('title_check')->setAttribute('value', 'no');
			}
			if ($text_check != 0)
				$wordform->get('text_check')->setAttribute('value', 'yes');
			else if ($text_check == 0 || !isset($data['text_check']) || $data['text_check'] == "no") {
				//$text_check = 0;
				$wordform->get('text_check')->setAttribute('value', 'no');
			}
			if ($tut_check != 0)
				$wordform->get('tut_check')->setAttribute('value', 'yes');
			else if ($tut_check == 0 || !isset($data['tut_check']) || $data['tut_check'] == "no") {
				//$text_check = 0;
				$wordform->get('tut_check')->setAttribute('value', 'no');
			}
			if ($yo != 0)
				$wordform->get('yo')->setAttribute('value', 'yes');
			else if ($yo == 0 || !isset($data['yo']) || $data['yo'] == "no") {
				//$text_check = 0;
				$wordform->get('yo')->setAttribute('value', 'no');
			}
			//print_r($data);
			if ($tab == 'dic')
				$articles = SearchController::getArticles($this->getServiceLocator(), $query, $title_check, $text_check, $yo, $tut_check, $page);
			else
				$articles = SearchController::getArticles($this->getServiceLocator(), $query, $title_check, $text_check, $yo, $tut_check, 1);
			$view->addChild($articles, 'articles');

			if ($tab == 'tutorial')
				$rules = $this->getRules($query, $title_check, $text_check, $yo, $tut_check, $page);
			else
				$rules = $this->getRules($query, $title_check, $text_check, $yo, $tut_check, 1);
			$view->addChild($rules, 'rules');
			
			$view->setVariable('tab', $tab);

        }
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
										'pag_part' => 'contents/paginator.phtml',
										'title' => $title, 
										'id' => $historic, 
										'pageCount' => count($this->paginator)));
						
		$articles->setTemplate('contents/articles');
		$view->addChild($articles, 'articles');
		return $view;       	
    }
    
}

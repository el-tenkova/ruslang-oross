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
        $view = new ViewModel();
/*       	$form = new SearchForm(null, HistoricTables::getAllHistoric($this->getServiceLocator()));
       	$view->setVariable('form', $form); 
        $request = $this->getRequest();
        if ($request->isPost()) {
        	//error_log("request is Post");
			$data = $request->getPost();
			$arts = array();
			$word = "";
			if (isset($data['submit']) || isset($data['submithist'])) {
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
				$articles = new ViewModel(array('articles' => $arts, 'title' => $title));
						
				$articles->setTemplate('contents/articles');
				$view->addChild($articles, 'articles');
			} 
        } */
		return $view;       	
    }
}

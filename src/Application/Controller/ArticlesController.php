<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

// use Contents\Model\ContentsTables;
// use Contents\Model\ParagraphTables;
 //use Contents\Model\RuleTables;
 //use Contents\Model\OrthogrTables;
 use Contents\Model\FormulaTables;
 use Contents\Model\ArticleTables;
 use Contents\Model\ArticleAddInfoTable;
 //use Contents\Model\WordTables;

 use Zend\Mvc\Controller\AbstractActionController;

 use Zend\View\Model\ViewModel;
 use Zend\View\Model\JsonModel;

 use Zend\Db\Sql\Sql;
 use Zend\Db\Adapter\Adapter;
 use Zend\Paginator;

 class ArticlesController extends AbstractActionController
 {
	 const FOR_PAGE_COUNT = 50;

     public function viewAction()
    {
        error_log("Articles viewAction");
        $request = $this->getRequest();
		$page = $this->params()->fromQuery('page', 1);
        $articles = ArticleAddInfoTable::getAll($this->getServiceLocator());
        $art_id = array();
        foreach ($articles as $art) {
            $art_id[] = array('id' => $art['id_article'], 'marks' => array());
        }
        $view = new ViewModel();        
		if (count($art_id)) {
			$paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($art_id));
			$paginator->setItemCountPerPage(self::FOR_PAGE_COUNT);
			$paginator->setCurrentPageNumber((int)$page);
            $show = [];
			foreach ($paginator->getCurrentItems() as $item) {
			    $show[] = $item;
            }
            $res = ArticleTables::getArticles($this->getServiceLocator(), $show, "");
            $articles = new ViewModel(array('articles' => $res, 
										'paginator' => $paginator, 
										'route' => 'article', 
										'action' => 'view', 
										'pag_part' => 'contents/paginator2.phtml',
										'title' => 'Статьи, ссылающиеся на дополнительные материалы', 
										'addinfo' => true,
										'pageCount' => count($paginator)));
						
		    $articles->setTemplate('contents/articles');
		    $view->addChild($articles, 'articles');
		   }
        return $view;        
     }
     
     public function srcAction()
     {
        error_log("Articles srcAction");
        
        if (isset($_POST['id'])) {
        //    error_log($_POST['id']);
            $src = ArticleTables::getSrc($this->getServiceLocator(), $_POST['id']);
            if ($src !== false) {
                $result = new JsonModel(array(
                    "src" => $src,
                    "success" => true,
                ));
                return $result;
            }
        }
        return new JsonModel(array(
                    'success' => false,
                ));
     }

     public function addinfoAction()
     {
        error_log("Articles addinfoAction");
        
        if (isset($_POST['id'])) {
     //       error_log($_POST['id']);
            $text = ArticleAddInfoTable::getTextById($this->getServiceLocator(), $_POST['id']);
            if ($text !== false) {
                $result = new JsonModel(array(
                    "text" => $text,
                    "success" => true,
                ));
                return $result;
            }
        }
        return new JsonModel(array(
                    'success' => false,
                ));
     }
     
 }
?>
<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

 use Contents\Model\OrthogrTables;
 use Contents\Model\FormulaTables;
 use Contents\Model\ArticlesFormulasTables;

 use Zend\Mvc\Controller\AbstractActionController;

 use Zend\View\Model\ViewModel;
 use Zend\View\Model\JsonModel;
 
 use Zend\Db\Sql\Sql;
 use Zend\Db\Adapter\Adapter;

 use Zend\Paginator;

 class OrthogrController extends AbstractActionController
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
        error_log("Orthogr indexAction");
        
        $view = new ViewModel();

        $formulas = new ViewModel(array('formulas' => FormulaTables::getAllFormulasWithInfo($this->getServiceLocator())));
        $formulas->setTemplate('contents/orthogr');
        $view->addChild($formulas, 'formulas');

        return $view;        
     }

     public function orthogrAction()
     {
        error_log("Orthogr orthogrAction");
        $view = new ViewModel();

		$id_ortho = $this->params()->fromQuery('id', 0); //$this->params('id');
		$page = $this->params()->fromQuery('page', 1);
		
		$word = OrthogrTables::getOrthogr($this->getServiceLocator(), $id_ortho);
		$formulas = FormulaTables::getFormulas($this->getServiceLocator(), $id_ortho);
		$id_formulas = array();
		foreach ($formulas as $formula) {
			$id_formulas[] = $formula['id'];
		}
		$arts = array();
		if (count($id_formulas) > 0)
			$arts = ArticlesFormulasTables::getArticlesForFormula($this->getServiceLocator(), $id_formulas, true, self::FOR_PAGE_COUNT, $page);

		$found = $arts['count'];	
		if ($found > 0) {
			$a = $found;
			while ($a > 100) {
				$a = $a % 10;
			}
			if ($a > 10 && $a < 20) 
				$title = sprintf('По орфограмме "%s" найдено %d статей', $word, $found);
			else {
				$a = $a % 10;
				if ($a == 1)
					$title = sprintf('По орфограмме "%s" найдена %d статья', $word, $found);
				else if ($a == 2 || $a == 3 || $a == 4)
					$title = sprintf('По орфограмме "%s" найдено %d статьи', $word, $found);
				else
					$title = sprintf('По орфограмме "%s" найдено %d статей', $word, $found);
			}
			$this->paginator = $arts['paginator'];
			$show = $arts['show'];
			$f = 'orthogr?id='.$id_ortho;
			foreach ($show as &$article) {
				$pos = strpos($article['article'], $f);
				if ($pos !== false) {
					$tmp = substr($article['article'], 0, $pos);
					$pos = strrpos($tmp, 'formula');
					$article['article'] = substr($article['article'], 0, $pos - 1).'marked '.substr($article['article'], $pos);
					//error_log($article['article']);
				}
			}
		}
		else {
			$title = sprintf('По формуле "%s" ничего не найдено', $word, count($arts));
		}				

		$articles = new ViewModel(array('articles' => $show, 
										'paginator' => $this->paginator, 
										'route' => 'orthogr',
										'action' => 'orthogr',
										'pag_part' => 'contents/paginator.phtml',
										'title' => $title, 
										'id' => $id_ortho, 
										'pageCount' => count($this->paginator), 
										'formula' => "1"));
		
/*		$articles = new ViewModel(array('title' => 'Статьи по формуле: '.FormulaTables::getFormula($this->getServiceLocator(), $id_formula),
		                                'articles' => $arts,
		                                'formula' => "1")); */
		                                
        $articles->setTemplate('contents/articles');
        $view->addChild($articles, 'articles');

        return $view;        
     }

     public function formulaAction()
     {
        error_log("Orthogr formulaAction");
        $view = new ViewModel();

		$id_formula = $this->params()->fromQuery('id', 0); //$this->params('id');
		$page = $this->params()->fromQuery('page', 1);
		
		$word = FormulaTables::getFormula($this->getServiceLocator(), $id_formula);
		$arts = ArticlesFormulasTables::getArticlesForFormula($this->getServiceLocator(), array($id_formula), true, self::FOR_PAGE_COUNT, $page);

		$found = $arts['count'];	
		if ($found > 0) {
			$a = $found;
			while ($a > 100) {
				$a = $a % 10;
			}
			if ($a > 10 && $a < 20) 
				$title = sprintf('По формуле "%s" найдено %d статей', $word, $found);
			else {
				$a = $a % 10;
				if ($a == 1)
					$title = sprintf('По формуле "%s" найдена %d статья', $word, $found);
				else if ($a == 2 || $a == 3 || $a == 4)
					$title = sprintf('По формуле "%s" найдено %d статьи', $word, $found);
				else
					$title = sprintf('По формуле "%s" найдено %d статей', $word, $found);
			}
			$this->paginator = $arts['paginator'];
			$show = $arts['show'];
			$f = 'formula?id='.$id_formula;
			foreach ($show as &$article) {
				$pos = strpos($article['article'], $f);
				if ($pos !== false) {
					$tmp = substr($article['article'], 0, $pos);
					$pos = strrpos($tmp, 'formula');
					$article['article'] = substr($article['article'], 0, $pos - 1).'marked '.substr($article['article'], $pos);
					//error_log($article['article']);
				}
			}
		}
		else {
			$title = sprintf('По формуле "%s" ничего не найдено', $word, count($arts));
		}				

		$articles = new ViewModel(array('articles' => $show, 
										'paginator' => $this->paginator, 
										'route' => 'orthogr',
										'action' => 'formula',
										'pag_part' => 'contents/paginator.phtml',
										'title' => $title, 
										'id' => $id_formula, 
										'pageCount' => count($this->paginator), 
										'formula' => "1"));
		
/*		$articles = new ViewModel(array('title' => 'Статьи по формуле: '.FormulaTables::getFormula($this->getServiceLocator(), $id_formula),
		                                'articles' => $arts,
		                                'formula' => "1")); */
		                                
        $articles->setTemplate('contents/articles');
        $view->addChild($articles, 'articles');

        return $view;        
     }
     
     public function viewAction()
     {
        error_log("Orthogr viewAction");
        if (isset($_POST['id'])) {
			$formulas = FormulaTables::getFormulas($this->getServiceLocator(), $_POST['id']);
			$act_formulas = array();
            if (count($formulas) > 0) {
				foreach ($formulas as &$formula) {
					$formula['act'] = '0';
				}
				if (isset($_POST['art'])) {
					$act_formulas = ArticlesFormulasTables::getFormulasForArticle($this->getServiceLocator(), $_POST['art'], $_POST['id']);
					foreach ($formulas as &$formula) {
						if (in_array($formula['id'], $act_formulas)) {
							$formula['act'] = '1';
						}
					}
				}
	            error_log($_POST['id']);
                $result = new JsonModel(array(
                    "formulas" => $formulas,
                    //"act_f" => $act_formulas,
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
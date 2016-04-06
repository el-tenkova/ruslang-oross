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

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Zend\Mvc\MvcEvent;

use Application\Form\SearchForm;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        error_log("IndexController : indexAction");
        $view = new ViewModel();
       	$form = new SearchForm();
       	$view->setVariable('form', $form); 
        $request = $this->getRequest();
        if ($request->isPost()) {
        	error_log("request is Post");
			//$data = $request->getPost();
			if (isset($data['word']) && strlen($data['word']) > 0) {
				$word = $data['word']; 
				$arts = WordTables::getArticles($this->getServiceLocator(), $word);
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
        }
        
        return $view;       	
    }
    
	public function searchAction()
	{
        error_log("searchAction");
		$request = $this->getRequest();
		if ($request->isPost()) {
			$data = $request->getPost();
			if (isset($data['word'])) {
				$word = $data['word']; 
				error_log($word);
			}
		}
	}
    
    public function paraAction()
    {
        error_log("paraAction");
        if (isset($_POST['para'])) {
            error_log($_POST['para']);
            $title = ParagraphTables::getParaTitle($this->getServiceLocator(), $_POST['para']);
            $rules = RuleTables::getRulesForPara($this->getServiceLocator(), $_POST['para']);
//            $orthos = OrthogrTables::getFormulasForPara($this->getServiceLocator(), $_POST['para']);
            if ($title !== false) {
                $result = new JsonModel(array(
                    "title" => $title,
                    "rules" => $rules,
                  //  "orthos" => $orthos,
                    "success" => true,
                ));
                return $result;
            }
        }
        return new JsonModel(array(
                    'success' => false,
                ));

    }        

    public function ruleAction()
    {
        error_log("ruleAction");
        if (isset($_POST['rule'])) {
            error_log($_POST['rule']);
            $rule = RuleTables::getRule($this->getServiceLocator(), $_POST['rule']);
            $orthos = OrthogrTables::getFormulasForRule($this->getServiceLocator(), $_POST['rule']);
            if ($rule !== false) {
                $result = new JsonModel(array(
                    "rule" => $rule,
                    "orthos" => $orthos,
                    "success" => true,
                ));
                return $result;
            }
        }
        return new JsonModel(array(
                    'success' => false,
                ));

    }        

    public function orthoAction()
    {
        error_log("orthoAction");
        if (isset($_POST['ortho'])) {
            error_log($_POST['ortho']);
            $ortho = OrthogrTables::getOrthogr($this->getServiceLocator(), $_POST['ortho']);
            $formulas = FormulaTables::getFormulas($this->getServiceLocator(), $_POST['ortho']);
            $result = new JsonModel(array(
                    "ortho" => $ortho,
                    "formulas" => $formulas,
                    "success" => true,
                ));
                return $result;
        }
        return new JsonModel(array(
                    'success' => false,
                ));

    }        

    public function wordAction()
    {
        error_log("wordAction");
        if (isset($_POST['word'])) {
            error_log($_POST['word']);
            $articles = WordTables::getArticles($this->getServiceLocator(), $_POST['word']);
            if (count($articles['articles']) > 0) {
                $orthos = array();
                if (count($articles['orthos']) > 0) {
                    foreach ($articles['orthos'] as $ortho_id) {
                        $ortho = OrthogrTables::getOrthogr($this->getServiceLocator(), $ortho_id);
                        $formulas = FormulaTables::getFormulas($this->getServiceLocator(), $ortho_id);
                        $orthos[] = array('id' => $ortho_id, 'ortho' => $ortho, 'formulas' => $formulas);
                    }
                }
                $paras = array();
                if (count($articles['paras']) > 0) {
                    foreach ($articles['paras'] as $para) {
                        $title = ParagraphTables::getParaTitle($this->getServiceLocator(), $para['id']);
                        $rules = RuleTables::getRulesForPara($this->getServiceLocator(), $para['id']);
                        error_log(sprintf("act_rules %d", count($para['rules'])));
                        $paras[] = array('id' => $para['id'], 'title' => $title, 'rules' => $rules, 'act_rules' => $para['rules']);
                    }
                }
                $result = new JsonModel(array(
                    "articles" => $articles['articles'],
                    "paras" => $paras,
//                    "rules" => $articles['rules'],
                    "orthos" => $orthos,
                    "success" => true,
                ));
                return $result;
            }
        }
        return new JsonModel(array(
                    'success' => false,
                ));

    }        

/*     public function getContentsTables()
     {
         //error_log("contentsTable123");
         if (!$this->contentsTables) {
             $sm = $this->getServiceLocator();
             $this->contentsTables = $sm->get('Contents\Model\ContentsTables');
         }
         return $this->contentsTables;
     }      */
    
}

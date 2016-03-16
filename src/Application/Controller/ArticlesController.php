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
 //use Contents\Model\WordTables;

 use Zend\Mvc\Controller\AbstractActionController;

 use Zend\View\Model\ViewModel;
 use Zend\View\Model\JsonModel;

 use Zend\Db\Sql\Sql;
 use Zend\Db\Adapter\Adapter;


 class ArticlesController extends AbstractActionController
 {

     public function viewAction()
     {
        error_log("Articles viewAction");
        
        $view = new ViewModel();

/*        $formulas = new ViewModel(array('formulas' => FormulaTables::getAllFormulasWithInfo($this->getServiceLocator())));
        $formulas->setTemplate('contents/orthogr');
        $view->addChild($formulas, 'formulas');
//             ->addChild($homeView, 'homeView');
//             ->addChild($secondarySidebarView, 'sidebar_secondary');
*/
        return $view;        
     }
     
     public function srcAction()
     {
        error_log("Articles srcAction");
        
        if (isset($_POST['id'])) {
            error_log($_POST['id']);
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
     
 }
?>
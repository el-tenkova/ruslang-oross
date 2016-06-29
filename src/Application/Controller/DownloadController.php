<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

 use Contents\Model\ArticleTables;
 use Contents\Model\HistoricTables;
 use Contents\Model\ArticlesFormulasTables;
 use Contents\Model\WordTables;
 use Contents\Model\FormulaTables;
 
 use Zend\Mvc\Controller\AbstractActionController;

 use Zend\View\Model\JsonModel;

 use Zend\Db\Sql\Sql;
 use Zend\Db\Adapter\Adapter;

 class DownloadController extends AbstractActionController
 {
     
     public function doAction()
     {
		error_log("Download doAction");
		if (isset($_POST['formula']) && isset($_POST['id_formula']) && $_POST['id_formula'] != "0") {
			//error_log($_POST['id_formula']);
			$ids = ArticlesFormulasTables::getArticlesForFormula($this->getServiceLocator(), array($_POST['id_formula']));
			//error_log(implode(",", $ids));
            $filename = ArticleTables::putArticlesToRtf($this->getServiceLocator(), implode(",", $ids), null, $_POST['id_formula'], null);
            if ($filename !== false) {
				return new JsonModel(array(
					'filename' => $filename,
					'success' => false,
				));
            }
		}
		if (isset($_POST['ortho']) && isset($_POST['id_ortho']) && $_POST['id_ortho'] != "0") {
			//error_log($_POST['id_formula']);
			$formulas = FormulaTables::getFormulas($this->getServiceLocator(), $_POST['id_ortho']);
			$id_formulas = array();
			foreach ($formulas as $formula) {
				$id_formulas[] = $formula['id'];
			}
			$ids = ArticlesFormulasTables::getArticlesForFormula($this->getServiceLocator(), $id_formulas);
			//error_log(implode(",", $ids));
            $filename = ArticleTables::putArticlesToRtf($this->getServiceLocator(), implode(",", $ids), null, null, $_POST['id_ortho']);
            if ($filename !== false) {
				return new JsonModel(array(
					'filename' => $filename,
					'success' => false,
				));
            }
		}
		if (isset($_POST['word']) && isset($_POST['query']) && strlen($_POST['query']) > 0) {
			$ids = WordTables::getArticles($this->getServiceLocator(), $_POST['query'], intval($_POST['title_check']) + intval($_POST['text_check']), $_POST['search_part']);
            $filename = ArticleTables::putArticlesToRtf($this->getServiceLocator(), implode(",", $ids), $_POST['word'], null, null);
            if ($filename !== false) {
				return new JsonModel(array(
					'filename' => $filename,
					'success' => false,
				));
            }
		}
		return new JsonModel(array(
			'success' => false,
		));
     }

 }
?>
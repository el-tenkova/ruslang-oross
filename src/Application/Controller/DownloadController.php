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
 
 use Zend\Mvc\Controller\AbstractActionController;

 use Zend\View\Model\JsonModel;

 use Zend\Db\Sql\Sql;
 use Zend\Db\Adapter\Adapter;

 class DownloadController extends AbstractActionController
 {
     
     public function doAction()
     {
		error_log("Download doAction");
		if (isset($_POST['hist']) && isset($_POST['id_hist']) && $_POST['id_hist'] != "0") {
			$ids = HistoricTables::getArticles($this->getServiceLocator(), $_POST['id_hist']);
            $filename = ArticleTables::putArticlesToRtf($this->getServiceLocator(), implode(",", $ids));
            if ($filename !== false) {
				return new JsonModel(array(
					'filename' => $filename,
					'success' => false,
				));
            }
		}
		if (isset($_POST['formula']) && isset($_POST['id_formula']) && $_POST['id_formula'] != "0") {
			$ids = ArticlesFormulasTables::getArticlesForFormula($this->getServiceLocator(), $_POST['id_formula']);
            $filename = ArticleTables::putArticlesToRtf($this->getServiceLocator(), implode(",", $ids));
            if ($filename !== false) {
				return new JsonModel(array(
					'filename' => $filename,
					'success' => false,
				));
            }
		}
		if (isset($_POST['word']) && isset($_POST['query']) && strlen($_POST['query']) > 0) {
			$ids = WordTables::getArticles($this->getServiceLocator(), $_POST['query'], intval($_POST['title_check']) + intval($_POST['text_check']), $_POST['search_part']);
            $filename = ArticleTables::putArticlesToRtf($this->getServiceLocator(), implode(",", $ids));
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
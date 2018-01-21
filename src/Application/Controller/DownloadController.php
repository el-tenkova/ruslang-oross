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
/*$ids = array(
1769,
8889,
21116,
28387,
37307,
47792,
53357,
53506,
53523,
54416,
54651,
64863,
64866,
99926,
135296,
139820,
151582,
173997,
184123,
192288,
194593);
/*
$ids1 = array(6207,10772,10799,10803,10812,10815,10822,10831,10833,10844,10860,10871,10887,15950,16300,16323,16379,21464,21467,21469,21472,35895,40163,40167,40316,46260,54255,55201,55226,55229,55418,55423,55427,55429,55435,55988,56229,57433,57440,57443,57677,57954,57973,57995,58019,58036,67009,67018,68092,68095,77478,78844,78848,82569,86890,88752,92709,98212,98213,98216,98932,99150,99152,99878,103989,104072,104317,104319,108382,108386,108390,108409,108413,119756,119770,119788,119791,119797,119838,119851,129762,130050,141066,141142,141144,141147,141153,143362,143364,143396,143424,143465,143505,143523,143539,143542,143565,143570,143573,143580,143583,143594,143599,143608,143616,143617,143631,143633,143636,143656,147168,147645,148241,150186,161793,161828,166325,170354,185968,186057,194020);
foreach ($ids as $item)
{
	$found = false;
	foreach ($ids1 as $id)
	{
		if ($item == $id)
		{
			$found = true;
			break;
		}
	}
		if ($found === false)
			error_log($item);
} */

			error_log(implode(",", $ids));
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
$ids = array(
1769,
8889,
21116,
28387,
37307,
47792,
53357,
53506,
53523,
54416,
54651,
64863,
64866,
99926,
135296,
139820,
151582,
173997,
184123,
192288,
194593);
			
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
//		    $ids = ArticleTables::getArticlesForRule($this->getServiceLocator(), 28);
			
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
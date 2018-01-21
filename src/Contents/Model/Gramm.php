<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

 namespace Contents\Model;
 
 use Contents\Model\BigrammTables;
 use Contents\Model\TrigrammTables;
 use Contents\Model\TetragrammTables;

 class Gramm
 {
     public static function getDicGramms($sm, $words, &$min, $yo)
     {     	
     	error_log(count($words));
     	error_log(sprintf("yo=%d", $yo));
		$min_arr = array();
		if (count($words) > 1) {
			$ret2 = BigrammTables::checkQuery($sm, $words, $min, $yo);
			$min_arr[] = $ret2['min'];
		}
		if (count($words) > 2) {
			$ret3 = TrigrammTables::checkQuery($sm, $words, $min, $yo);
			$min_arr[] = $ret3['min'];
		}
		if (count($words) > 3) {
			$ret4 = TetragrammTables::checkQuery($sm, $words, $min, $yo);
			$min_arr[] = $ret4['min'];
		}
		$min_gr = -1;
		$min_gr_idx = -1;
		for ($i = 0; $i < count($min_arr); $i++)
		{
			if ($min_gr == -1 && $min_arr[$i] != -1) {
				$min_gr = $min_arr[$i];
				$min_gr_idx = $i;
				continue;
			}
			if ($min_arr[$i] != -1 && $min_arr[$i] <= $min_gr) {
				$min_gr = $min_arr[$i];
				$min_gr_idx = $i;
			}
		}
		error_log(sprintf("min after gramms = %d, idx = %d", $min_gr, $min_gr_idx));
		if ($min_gr != -1)
			$min = $min_gr;
		switch ($min_gr_idx) {
			case 0: // bigramms
				$ids = BigrammTables::getIdsArray($sm, $words, $ret2['min_idx'], $ret2['gramm'], $yo);
				if (count($words) > 2 && $min_gr > MAX_ARTS)
					return array('stop' => 1);
				error_log("after 	BigrammTables::getIdsArray");
				return array('gramms' => $ids['ids'], 'delta' => 2, 'start_gr' => $ids['start_gr'], 'nwords' => $ids['words']);
			case 1: // trigramms
				if (count($words) > 3 && $min_gr > MAX_ARTS)
					return array('stop' => 1);
				error_log($ret3['min_idx']);
				$ids = TrigrammTables::getIdsArray($sm, $words, $ret3['min_idx'], $ret3['gramm'], $yo);
				return array('gramms' => $ids['ids'], 'delta' => 3, 'start_gr' => $ids['start_gr'], 'nwords' => $ids['words']);
			case 2: // tetragramms
				if (count($words) > 4 && $min_gr > MAX_ARTS)
					return array('stop' => 1);
				error_log($ret4['min_idx']);
				$ids = TetragrammTables::getIdsArray($sm, $words, $ret4['min_idx'], $ret4['gramm'], $yo);
				return array('gramms' => $ids['ids'], 'delta' => 4, 'start_gr' => $ids['start_gr'], 'nwords' => $ids['words']);
			default:
				break;
		}
		return array();
	}
	
	public static function getArticlesId($sm, $delta, $gramma, $where, $yo)
	{
		switch ($delta) {
			case 2:
				return BigrammTables::getArticles($sm, $gramma, $where, $yo);
			case 3:
				return TrigrammTables::getArticles($sm, $gramma, $where, $yo);
			case 4:
				return TetragrammTables::getArticles($sm, $gramma, $where, $yo);
			
		}
		return array();
	}
	public static function getGrammArticles($sm, $delta, $item, $where, $id_arts)
	{	
		switch ($delta) {
			case 2:
				return BigrammTables::getPureArticles($sm, $item, $where, $id_arts);
				
			case 3:
				return TrigrammTables::getPureArticles($sm, $item, $where, $id_arts);
			
			case 4:
				return TetragrammTables::getPureArticles($sm, $item, $where, $id_arts);
			  default:
			  	return array();
			    	
		}
	}
 }
 ?>
 
<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Contents\Model;

 use Zend\Db\TableGateway\TableGateway;
 use Zend\Db\Sql\Select;
 use Zend\Db\Sql\Delete;
 use Zend\Db\Sql\Sql;
 use Zend\Db\Sql\Expression;
 
 class TrigrammTables
 {
    protected $tableGateway;
	

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
     
	public static function checkQuery($sm, $words, $min_word, $yo)
	{
		$min = -1;
		$min_idx = 0;
		if (count($words) < 3)
			return array('min' => -1);
			
		$trigramms = array();
		if (count($words) == 3)
			$trigramms[] = $words[0].$words[1].$words[2];
		else {
			foreach ($words as $word) {
		        $word = trim($word, " \t.,:;-");
				if (count($trigramms) > 0) {
					$trigramms[count($trigramms) - 1] .= $word;
					if (count($trigramms) - 1 > 0)
						$trigramms[count($trigramms) - 2] .= $word;
				}
				$trigramms[] = $word;
			}
		}
		$table = $sm->get('Contents\Model\TrigrammTables');
		$idx = 0;
		foreach ($trigramms as $trigramm) {
	        $num = $table->tableGateway->select(function(Select $select) use ($trigramm, $yo)
	        {
			//	$select->columns(array('art_count'));
				if (intval($yo) == 1)
					$select->where('tri.trigramm REGEXP \'^'.$trigramm.'$\'');
				else
	            	$select->where('tri.trigramm LIKE \''.$trigramm.'\'');
	        });
	        error_log(sprintf("trigr num = %d", count($num)));
	        if (count($num) > 0)
	        {
		        if ($min == -1) {
		        	$min = $num->current()->art_count;
		        	$min_idx = $idx;
		        }
		        elseif ($min > $num->current()->art_count) {
		        	$min = $num->current()->art_count;
		        	$min_idx = $idx;
		        }		
		        error_log(sprintf("trigr art_count = %d min_idx = %d", $num->current()->art_count, $min_idx));
	        }
	        $idx++;
	        if ($idx == count($words) - 2)
	        	break;
		}
		return array('min' => $min, 'gramm' => $trigramms[$min_idx], 'min_idx' => $min_idx);
	}
			
	public static function getIdsArray($sm, $words, $min_idx, $gramm, $yo)
	{
		$table = $sm->get('Contents\Model\TrigrammTables');
		
		$id_articles = $table->tableGateway->select(function(Select $select) use ($gramm, $yo)
		{
			$select->columns(array('art_count'));
	        $select->join(array('trw' => 'trigramms_articles'), 'tri.id = trw.id', array('id_article'), 'left'); 
	        if (intval($yo) == 1)
	        	$select->where('tri.trigramm REGEXP \'^'.$gramm.'$\'');
	        else
	        	$select->where('tri.trigramm LIKE \''.$gramm.'\'');
		});
		$ids = array();
	    foreach ($id_articles as $id) {
	    	$ids[] = $id->id_article;
	    }
	    $words_new = array();
	    $idx = 0;
	    foreach ($words as $word) {
	    	if ($idx == $min_idx) {
	    		$words_new[] = $gramm;
	    	}
	    	elseif ($idx != $min_idx + 1 && $idx != $min_idx + 2) {
	    		$words_new[] = $word;
	    	}
	    	$idx++;
	    }	
	    error_log(sprintf("from trigrammTables %d", count($words_new)));   	
	    if (count($ids) > 0) {
	    	if (count($words_new) == 1)
	    		return array('ids' => "", 'words' => $words_new, 'start_gr' => $min_idx);
	    	else
			    return array('ids' => implode(",", $ids), 'words' => $words_new, 'start_gr' => $min_idx);
	    }
		return array();
	}

	public static function getArticles($sm, $query, $where, $yo)
	{
		$table = $sm->get('Contents\Model\TrigrammTables');
		
	   	$id_array = array();
	   	$id_arts = array();
		
		$bw_ON = 'tri.id = trw.id';
		if ($where != 3) {
			if ($where == 1)
				$bw_ON .= ' AND trw.title = 1';
			else
				$bw_ON .= ' AND (trw.title = 2 OR trw.title = 3)'; 	
		}
		if (intval($yo) == 1)
			$w_LIKE = 'tri.trigramm REGEXP \'^'.$query.'$\'';
		else
			$w_LIKE = 'tri.trigramm LIKE \''.$query.'\'';
	
		$articles = $table->tableGateway->select(function(Select $select) use ($bw_ON, $w_LIKE)
		{
			$select->join(array('trw' => 'trigramms_articles'), new Expression($bw_ON), array('id_article', 'start', 'len', 'title', 'segment', 'number'), 'left'); 
			$select->order('trw.title, trw.id_article, trw.start');
			$select->where($w_LIKE);
		});
		       // print_r($articles);
		error_log(count($articles));
	//	        $id_array = array();
		$artId = 0;
		$id_arts = array();
		foreach ($articles as $article) {
			if (isset($article->id_article)) {
			        		//error_log(sprintf("id_article = %d", $article->id_article));
				if ($artId != $article->id_article) {
					$id_array[] = array('id' => $article->id_article, 'marks' => array(array('start' => $article->start, 'len' => $article->len, 'title' => $article->title, 'step' => 0, 'segment' => $article->segment, 'number' => $article->number, 'space' => 0)));
					$id_arts[] = $article->id_article;
					$artId = $article->id_article;
				}
				else {
					$add = true;
					// gamma-luchi & luchi
					foreach ($id_array[count($id_array) - 1]['marks'] as $mark) {
						if ($mark['start'] == $article->start) {
							$add = false;
							break;
						}
					}
					if ($add === true) {
						$id_array[count($id_array) - 1]['marks'][] =  array('start' => $article->start, 'len' => $article->len, 'title' => $article->title, 'step' => 0, 'segment' => $article->segment, 'number' => $article->number, 'space' => 0);
					}
				}
			}	
		}
		return $id_array;
	}
	public static function getPureArticles($sm, $query, $where)
	{
		$table = $sm->get('Contents\Model\TrigrammTables');
		
	   	$id_array = array();
	   	$id_arts = array();
		
		$bw_ON = 'tri.id = trw.id';
		if ($where != 3) {
			if ($where == 1)
				$bw_ON .= ' AND trw.title = 1';
			else
				$bw_ON .= ' AND (trw.title = 2 OR trw.title = 3)'; 	
		}
		$w_LIKE = 'tri.trigramm LIKE \''.$query.'\'';
	
		$articles = $table->tableGateway->select(function(Select $select) use ($bw_ON, $w_LIKE)
		{
			$select->join(array('trw' => 'trigramms_articles'), new Expression($bw_ON), array('id_article', 'start', 'len', 'title', 'segment', 'number'), 'left'); 
			$select->order('trw.title, trw.id_article, trw.start');
			$select->where($w_LIKE);
		});
		
		return $articles;
	}
	public static function deleteArticle($sm, $id)
	{
		$action = new Delete('trigramms_articles');
        $action->where(array('id_article = ?' => $id));

        $sql    = new Sql($sm->get('Zend\Db\Adapter\Adapter'));
        $stmt   = $sql->prepareStatementForSqlObject($action);
        $result = $stmt->execute();		
	}
 } 
 
 ?>
 
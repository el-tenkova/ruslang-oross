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
 
 class TetragrammTables
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
		$idx = 0;
		$min_idx = 0;
		if (count($words) < 4)
			return array('min' => -1);
			
		$tetragramms = array();
		if (count($words) == 4)
			$tetragramms[] = $words[0].$words[1].$words[2].$words[3];
		else {
			foreach ($words as $word) {
		        $word = trim($word, " \t.,:;-");
				if (count($tetragramms) > 0) {
					$tetragramms[count($tetragramms) - 1] .= $word;
					if (count($tetragramms) - 1 > 0)
						$tetragramms[count($tetragramms) - 2] .= $word;
					if (count($tetragramms) - 2 > 0)
						$tetragramms[count($tetragramms) - 3] .= $word;
				}
				$tetragramms[] = $word;
			}
		}		
		$table = $sm->get('Contents\Model\TetragrammTables');
		foreach ($tetragramms as $tetragramm) {
	        $num = $table->tableGateway->select(function(Select $select) use ($tetragramm, $yo)
	        {
			//	$select->columns(array('art_count'));
				if (intval($yo) == 1)
					$select->where('t.tetragramm REGEXP \'^'.$tetragramm.'$\'');
				else
	            	$select->where('t.tetragramm LIKE \''.$tetragramm.'\'');
	        });
	        error_log(sprintf("tetragr %s num = %d", $tetragramm, count($num)));
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
		        error_log(sprintf("tetragr art_count = %d", $num->current()->art_count));
	        }
	        $idx++;
	        if ($idx == count($words) - 3)
	        	break;
	        
		}
		return array('min' => $min, 'gramm' => $tetragramms[$min_idx], 'min_idx' => $min_idx);
	}
		
	public static function getIdsArray($sm, $words, $min_idx, $gramm, $yo)
	{
		$table = $sm->get('Contents\Model\TetragrammTables');
		
		$id_articles = $table->tableGateway->select(function(Select $select) use ($gramm, $yo)
		{
			$select->columns(array('art_count'));
	        $select->join(array('tw' => 'tetragramms_articles'), 't.id = tw.id', array('id_article'), 'left'); 
	        if (intval($yo))
		        $select->where('t.tetragramm REGEXP \'^'.$gramm.'$\'');
	        else
	        	$select->where('t.tetragramm LIKE \''.$gramm.'\'');
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
	    	elseif ($idx != $min_idx + 1 && $idx != $min_idx + 2 && $idx != $min_idx + 3) {
	    		$words_new[] = $word;
	    	}
	    	$idx++;
	    }	
	    error_log(sprintf("from tetragrammTables %d %d", count($words_new), count($ids)));   	
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
		$table = $sm->get('Contents\Model\TetragrammTables');
		
	   	$id_array = array();
	   	$id_arts = array();
		
		$bw_ON = 't.id = tw.id';
		if ($where != 3) {
			if ($where == 1)
				$bw_ON .= ' AND tw.title = 1';
			else
				$bw_ON .= ' AND (tw.title = 2 OR tw.title = 3)'; 	
		}
		if (intval($yo) == 1)
			$w_LIKE = 't.tetragramm REGEXP \'^'.$query.'$\'';
		else
			$w_LIKE = 't.tetragramm LIKE \''.$query.'\'';
		if (count($id_arts) != 0) {
			$w_LIKE .= " AND aw.id_article IN (".implode(",", $id_arts).")"; 
		}
	
		$articles = $table->tableGateway->select(function(Select $select) use ($bw_ON, $w_LIKE)
		{
			$select->join(array('tw' => 'tetragramms_articles'), new Expression($bw_ON), array('id_article', 'start', 'len', 'title', 'segment', 'number'), 'left'); 
			$select->order('tw.title, tw.id_article, tw.start');
			$select->where($w_LIKE);
		});
		       // print_r($articles);
		error_log(sprintf("tetragramm count of articles = %d", count($articles)));
	//	        $id_array = array();
		$artId = 0;
		$id_arts = array();
		foreach ($articles as $article) {
			if (isset($article->id_article)) {
			        		//error_log(sprintf("id_article = %d", $article->id_article));
				if ($artId != $article->id_article) {
					$id_array[] = array('id' => $article->id_article, 'marks' => array(array('start' => $article->start, 'len' => $article->len, 'title' => $article->title, 'step' => 0, 'segment' => $article->segment, 'number' => $article->number, 'space' => 0, 'delta' => 4)));
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
						$id_array[count($id_array) - 1]['marks'][] =  array('start' => $article->start, 'len' => $article->len, 'title' => $article->title, 'step' => 0, 'segment' => $article->segment, 'number' => $article->number, 'space' => 0, 'delta' => 4);
					}
				}
			}	
		}
//		print_r($id_array);
		return $id_array;
	}
     
	public static function getPureArticles($sm, $query, $where, $id_arts)
	{
		$table = $sm->get('Contents\Model\TetragrammTables');
		
	   	$id_array = array();
	   	$id_arts = array();
		
		$bw_ON = 't.id = tw.id';
		if ($where != 3) {
			if ($where == 1)
				$bw_ON .= ' AND tw.title = 1';
			else
				$bw_ON .= ' AND (tw.title = 2 OR tw.title = 3)'; 	
		}
		$w_LIKE = 't.tetragramm LIKE \''.$query.'\'';
		if (count($id_arts) != 0) {
			$w_LIKE .= " AND aw.id_article IN (".implode(",", $id_arts).")"; 
		}
	
		$articles = $table->tableGateway->select(function(Select $select) use ($bw_ON, $w_LIKE)
		{
			$select->join(array('tw' => 'tetragramms_articles'), new Expression($bw_ON), array('id_article', 'start', 'len', 'title', 'segment', 'number'), 'left'); 
			$select->order('tw.title, tw.id_article, tw.start');
			$select->where($w_LIKE);
		});
		return $articles;
	}          
 	public static function deleteArticle($sm, $id)
	{
		$action = new Delete('tetragramms_articles');
        $action->where(array('id_article = ?' => $id));

         $sql    = new Sql($sm->get('Zend\Db\Adapter\Adapter'));
         $stmt   = $sql->prepareStatementForSqlObject($action);
         $result = $stmt->execute();		
	}	    
} 
 
 ?>
 
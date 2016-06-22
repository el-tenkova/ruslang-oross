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
 use Zend\Db\Sql\Sql;
 use Zend\Db\Sql\Expression;
 
 class BigrammTables
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
     
	public static function checkQuery($sm, $words, $min_word)
	{
		$min = -1;
		$idx = 0;
		$min_idx = 0;
		if (count($words) < 2)
			return array('min' == -1);
			
		$bigramms = array();
		if (count($words) == 2)
			$bigramms[] = $words[0].$words[1];
		else {
			foreach ($words as $word) {
		        $word = trim($word, " \t.,:;-");
				if (count($bigramms) > 0) {
					$bigramms[count($bigramms) - 1] .= $word;
				}
				$bigramms[] = $word;
			}
		}
		$table = $sm->get('Contents\Model\BigrammTables');
		foreach ($bigramms as $bigramm) {
	        $num = $table->tableGateway->select(function(Select $select) use ($bigramm)
	        {
			//	$select->columns(array('art_count'));
	            $select->where('b.bigramm LIKE \''.$bigramm.'\'');
	        });
	        error_log(sprintf("brg num = %d", count($num)));
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
		        error_log(sprintf("brg art_count = %d", $num->current()->art_count));
	        }
	        $idx++;
	        if ($idx == count($words) - 1)
	        	break;
	        
		}
		return array('min' => $min, 'gramm' => $bigramms[$min_idx], 'min_idx' => $min_idx);
	}
	
	public static function getIdsArray($sm, $words, $min_idx, $gramm)
	{
		$table = $sm->get('Contents\Model\BigrammTables');
		$id_articles = $table->tableGateway->select(function(Select $select) use ($gramm)
		{
			$select->columns(array('art_count'));
	        $select->join(array('bw' => 'bigramms_articles'), 'b.id = bw.id', array('id_article'), 'left'); 
	        $select->where('b.bigramm LIKE \''.$gramm.'\'');
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
	    	elseif ($idx != $min_idx + 1) {
	    		$words_new[] = $word;
	    	}
	    	$idx++;
	    	
	    }	
	    error_log(sprintf("from bigrammTables %d", count($words_new)));   	
	    if (count($ids) > 0) {
	    	if (count($words_new) == 1)
	    		return array('ids' => "", 'words' => $words_new, 'start_gr' => $min_idx);
	    	else
			    return array('ids' => implode(",", $ids), 'words' => $words_new, 'start_gr' => $min_idx);
	    }
		return array();
	}

	public static function getArticles($sm, $query, $where)
	{
		$table = $sm->get('Contents\Model\BigrammTables');
		
	   	$id_array = array();
	   	$id_arts = array();
		
		$bw_ON = 'b.id = bw.id';
		if ($where != 3) {
			if ($where == 1)
				$bw_ON .= ' AND bw.title = 1';
			else
				$bw_ON .= ' AND (bw.title = 2 OR bw.title = 3)'; 	
		}
		$w_LIKE = 'b.bigramm LIKE \''.$query.'\'';
	
		$articles = $table->tableGateway->select(function(Select $select) use ($bw_ON, $w_LIKE)
		{
			$select->join(array('bw' => 'bigramms_articles'), new Expression($bw_ON), array('id_article', 'start', 'len', 'title', 'segment', 'number'), 'left'); 
			$select->order('bw.title, bw.id_article, bw.start');
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
		$table = $sm->get('Contents\Model\BigrammTables');
		
	   	$id_array = array();
	   	$id_arts = array();
		
		$bw_ON = 'b.id = bw.id';
		if ($where != 3) {
			if ($where == 1)
				$bw_ON .= ' AND bw.title = 1';
			else
				$bw_ON .= ' AND (bw.title = 2 OR bw.title = 3)'; 	
		}
		$w_LIKE = 'b.bigramm LIKE \''.$query.'\'';
	
		$articles = $table->tableGateway->select(function(Select $select) use ($bw_ON, $w_LIKE)
		{
			$select->join(array('bw' => 'bigramms_articles'), new Expression($bw_ON), array('id_article', 'start', 'len', 'title', 'segment', 'number'), 'left'); 
			$select->order('bw.title, bw.id_article, bw.start');
			$select->where($w_LIKE);
		});
		return $articles;
	}         
 } 
 
 ?>
 
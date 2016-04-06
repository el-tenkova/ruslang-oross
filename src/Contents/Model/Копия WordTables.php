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

 const typeTitle = 1;
 const typeText = 2;
 //
 const typeRule = 4;
 const typeFormula = 5;
 const typeFormulaExample = 6;
 const typeFootNote = 7;
 const typeOrtho = 8;
 
 class WordTables
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
     
	public static function processSpecSym($query)
	{
		if (strpos($query, "?") !== false || strpos($query, "*") !== false) {
			$LIKE = 'w.word REGEXP \'^'.$query.'$\'';
			if (strpos($query, "*") !== false) {
				if (strpos($query, "*") == 0 && strrpos($query, "*") == strlen($query) - 1) {
					$LIKE = 'w.word REGEXP \''.$query.'\'';
				}
				else if (strpos($query, "*") == 0) {
					$LIKE = 'w.word REGEXP \''.$query.'$\'';
				}
				else if (strrpos($query, "*") == strlen($query) - 1) {
					$LIKE = 'w.word REGEXP \'^'.$query.'\'';
				}
				$LIKE = str_replace("*", "[[:alpha:]{0,2}|[:punct:]|[:space:]|-]*", $LIKE);
			}		        
			if (strpos($query, "?") !== false) {
					//$w_LIKE = str_replace("?", "[[:alpha:]]{0,2}", $w_LIKE);
				$LIKE = str_replace("?", "[[:alpha:]|[:punct:]|[:space:]|-]{0,2}", $LIKE);
			}
		}
		else {
			$LIKE = 'w.word LIKE \''.$query.'\'';
		}
		return $LIKE;
	}

	public static function fillResArray($id_array, $step, $type = false)
	{
		$res_id = array();
//		error_log(sprintf("step = %d", $step));
		foreach ($id_array as &$item) {
			$start = 0;
			foreach ($item['marks'] as &$mark) {
				if ($type !== false && $mark['type'] != $type)
					continue;
				//error_log(sprintf("mark[step] = %d", $mark['step']));
				if ($mark['step'] == 0) {
//					$segment = $mark['segment'];
					$number = $mark['number'];
					for ($i = $start + 1, $j = 1; $i < count($item['marks']) && $j < $step; $i++, $j++) {
	//					error_log($end);
	//					error_log($item['marks'][$i]['start'] - $end);
//						error_log($item['marks'][$i]['len']);
						error_log(sprintf("id = %d, %d, %d, %d", $item['id'], $item['marks'][$i]['step'], $item['marks'][$i]['number'], $item['marks'][$i]['start']));
//						error_log($item['marks'][$i]['number']);
						if ($item['marks'][$i]['step'] != $j) { // || $item['marks'][$i]['start'] - $end > $item['marks'][$i]['len']) {
							break;
						}
						if ($item['marks'][$i]['number'] != $number + 1) {
							break;
						}
						$number++;
					}
					if ($j == $step) {
						if (count($res_id) == 0) {
							//error_log("fill res_id");
							$res_id[] = array('id' => $item['id'], 'marks' => array());
							for ($i = $start, $j = 0; $i < count($item['marks']) && $j < $step; $i++, $j++) {
								$res_id[count($res_id) - 1]['marks'][] = $item['marks'][$i];
								//error_log(sprintf("i = %d", $i));
							}
						}
						else {
							//error_log("fill res_id 1");
							$done = false;
							foreach ($res_id as &$res_item) {
								if ($res_item['id'] == $item['id']) {
									for ($i = $start, $j = 0; $i < count($item['marks']) && $j < $step; $i++, $j++) {
										$res_item['marks'][] = $item['marks'][$i];
									}
									$done = true;
								}
							}
							if ($done === false) {
								$res_id[] = array('id' => $item['id'], 'marks' => array());
								for ($i = $start, $j = 0; $i < count($item['marks']) && $j < $step; $i++, $j++) {
									$res_id[count($res_id) - 1]['marks'][] = $item['marks'][$i];
									//error_log(sprintf("i = %d", $i));
								}
							}
						}
					}
				}
				$start ++;
			}
		}
		return $res_id;
	}
	
	public static function addMark($res_id, $other_id, $mark)
	{
		$sign = "";
		switch ($mark['type']) {
			case typeFormula:
				$sign = 'formula';
				break;					
			case typeFormulaExample:
				$sign = 'example';
				break;
			case typeFootNote:
				$sign = 'foot';
				break;
			case typeOrtho:
				$sign = 'ortho';
				break;
		}
		$found = false;
		foreach ($res_id as &$item) {
			if ($item['id'] == $mark['rule']) {
				$found = true;
				error_log("rule found");
				if (!isset($item[$sign])) {
					$item[$sign] = array(array('id' => $other_id, 'marks' => array($mark)));
					error_log($sign);
				}
				else {
					$found_spec = false;
					foreach ($item[$sign] as &$spec) {
						if ($spec['id'] == $other_id) {
							$found_spec = true;
							$spec['marks'][] = $mark;
							break;
						}
					}
					if (!$found_spec) {
						$item[$sign][] = array('id' => $other_id, 'marks' => array($mark));
					}
				}
				break;
			}
		}
		if ($found === false) {
			if ($mark['rule'] != 0) {
				error_log(sprintf("add rule = %d", $mark['rule']));
				$res_id[] = array('id' => strval($mark['rule']), 'marks' => array(), $sign => array(array('id' => strval($other_id), 'marks' => array($mark))));
			}
		}
		return $res_id;
		
	}
	
    public static function getArticles($sm, $word, $where = 3, $paginated = false, $count_for_page = 0, $page = 0)
    {
		error_log("WordTables: getArticles");
     //   error_log(sprintf("getArticles, word = %s, where = %d, search_part = %s", $word, $where, $search_part));
       	$query = $word;
       	$table = $sm->get('Contents\Model\WordTables');
       	$word = trim($word, " \t.-");
       	$words = explode(" ", $word);
//        $word = str_replace("-", "", $word);
//        $word = str_replace(" ", "", $word);
//        $word = str_replace(".", "", $word);

	   	$id_array = array();
	   	$id_arts = array();
		$step = 0;
		foreach ($words as &$item) {
//			if ($step != 0 && count($id_arts) == 0)
//!!!				break;
			
        	$item = trim($item, " \t.,:;-");
            if (strlen($item) == 0)
            	continue;
	        $aw_ON = 'w.id = aw.id';
	        if ($where != 3) {
	        	if ($where == 1)
	        		$aw_ON .= ' AND aw.title = 1';
	        	else
	        		$aw_ON .= ' AND (aw.title = 2 OR aw.title = 3)'; 	
	        }
			$w_LIKE = WordTables::processSpecSym($item);//w.word LIKE \''.$item.'\'';

	        //error_log(sprintf(" %s, %d", $aw_ON, $where));
/*	        $w_LIKE = 'w.word LIKE \''.$item.'\'';
	        if (strpos($item, "?") !== false || strpos($item, "*") !== false) {
		        $w_LIKE = 'w.word REGEXP \'^'.$item.'$\'';
		        if (strpos($item, "*") !== false) {
			        if (strpos($item, "*") == 0 && strrpos($item, "*") == strlen($item) - 1) {
				        $w_LIKE = 'w.word REGEXP \''.$item.'\'';
			        }
			        else if (strpos($item, "*") == 0) {
				        	$w_LIKE = 'w.word REGEXP \''.$item.'$\'';
				    }
				    else if (strrpos($item, "*") == strlen($item) - 1) {
						$w_LIKE = 'w.word REGEXP \'^'.$item.'\'';
			        }
					$w_LIKE = str_replace("*", "[[:alpha:]|[:punct:]|[:space:]|-]{0,2}*", $w_LIKE);
		        }		        
	        	if (strpos($item, "?") !== false) {
					//$w_LIKE = str_replace("?", "[[:alpha:]]{0,2}", $w_LIKE);
					$w_LIKE = str_replace("?", "[[:alpha:]|[:punct:]|[:space:]|-]{0,2}", $w_LIKE);
	        	}
	        } */
	        if (count($id_arts) != 0) {
	        	$w_LIKE .= " AND a.id IN (".implode(",", $id_arts).")"; 
	        }
	        error_log($w_LIKE);
	        $articles = $table->tableGateway->select(function(Select $select) use ($aw_ON, $w_LIKE)
	        {
	        	$select->join(array('aw' => 'words_articles'), new Expression($aw_ON), array('id_article', 'start', 'len', 'title', 'segment', 'number'), 'left'); 
	            $select->join(array('a' => 'articles'), 'aw.id_article = a.id', array('id'), 'left');
	            $select->where($w_LIKE);
	            $select->order('aw.title, aw.id_article, aw.start');
//	            $select->order('aw.id_article, aw.title, aw.start');
	        });
			error_log(count($articles));
//	        $id_array = array();
	        $artId = 0;
	        $id_arts = array();
	        if ($step == 0) {
		        foreach ($articles as $article) {
		        	if (isset($article->id_article)) {
		        		if ($artId != $article->id_article) {
			        		$id_array[] = array('id' => $article->id_article, 'marks' => array(array('start' => $article->start, 'len' => $article->len, 'title' => $article->title, 'step' => $step, 'segment' => $article->segment, 'number' => $article->number)));
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
			        			$id_array[count($id_array) - 1]['marks'][] =  array('start' => $article->start, 'len' => $article->len, 'title' => $article->title, 'step' => $step, 'segment' => $article->segment, 'number' => $article->number);
		        			}
		        		}
		        	}
		        }
	        }
	        else {
		        foreach ($articles as $article) {
		        	if (isset($article->id_article)) {
		        		foreach ($id_array as &$id) {
		        			if ($id['id'] == $article->id_article) {
		        				$ins_idx = 0;
		        				foreach ($id['marks'] as $mark) {
		        			//print_r($mark);
		        					if ($mark['start'] > $article->start) {
		        						break;
		        					}
									$ins_idx ++;
	
		        				}
		        				error_log(sprintf("ins_idx = %d count = %d", $ins_idx, count($id['marks'])));
		        				if ($ins_idx == count($id['marks'])) {
		        					$id['marks'][] = array('start' => $article->start, 'len' => $article->len, 'title' => $article->title, 'step' => $step, 'segment' => $article->segment, 'number' => $article->number);
		        				}
		        				else {
		        					error_log(sprintf("slice ins_idx = %d", $ins_idx));
//		        					print_r(array_slice($id['marks'], 0, $ins_idx, true));
//		        					print_r(array_slice($id['marks'], $ins_idx, count($id['marks']) - 1, true));
								//	print_r($id['marks']);
									array_splice($id['marks'], $ins_idx, 0, array(array('start' => $article->start, 'len' => $article->len, 'title' => $article->title, 'step' => $step, 'segment' => $article->segment, 'number' => $article->number)));
							//		print_r($id['marks']);
		        				}
		        				error_log(sprintf("ins_idx = %d count = %d", $ins_idx, count($id['marks'])));
		        			}
		        		}

		        	}
		        }
	        }
	        $step ++;
		}
		$res_id = WordTables::fillResArray($id_array, $step);
//		error_log(sprintf("step = %d", $step));
/*		foreach ($id_array as &$item) {
			$start = 0;
			foreach ($item['marks'] as &$mark) {
				//error_log(sprintf("mark[step] = %d", $mark['step']));
				if ($mark['step'] == 0) {
					$segment = $mark['segment'];
					$number = $mark['number'];
					for ($i = $start + 1, $j = 1; $i < count($item['marks']) && $j < $step; $i++, $j++) {
	//					error_log($end);
	//					error_log($item['marks'][$i]['start'] - $end);
	//					error_log($item['marks'][$i]['len']);
						if ($item['marks'][$i]['step'] != $j) { // || $item['marks'][$i]['start'] - $end > $item['marks'][$i]['len']) {
							break;
						}
						if ($item['marks'][$i]['number'] != $number + 1) {
							break;
						}
						$number++;
					}
					if ($j == $step) {
						if (count($res_id) == 0) {
							error_log("fill res_id");
							$res_id[] = array('id' => $item['id'], 'marks' => array());
							for ($i = $start, $j = 0; $i < count($item['marks']) && $j < $step; $i++, $j++) {
								$res_id[count($res_id) - 1]['marks'][] = $item['marks'][$i];
								//error_log(sprintf("i = %d", $i));
							}
						}
						else {
							error_log("fill res_id 1");
							$done = false;
							foreach ($res_id as &$res_item) {
								if ($res_item['id'] == $item['id']) {
									for ($i = $start, $j = 0; $i < count($item['marks']) && $j < $step; $i++, $j++) {
										$res_item['marks'][] = $item['marks'][$i];
									}
									$done = true;
								}
							}
							if ($done === false) {
								$res_id[] = array('id' => $item['id'], 'marks' => array());
								for ($i = $start, $j = 0; $i < count($item['marks']) && $j < $step; $i++, $j++) {
									$res_id[count($res_id) - 1]['marks'][] = $item['marks'][$i];
									//error_log(sprintf("i = %d", $i));
								}
							}
						}
					}
				}
				$start ++;
			}
		} */
//print_r($res_id);
		foreach ($res_id as $item) {
			foreach ($item['marks'] as $mark)
				error_log(sprintf("mark = %d, %d, %d, %d, %d, %d, %d", $item['id'], $mark['start'], $mark['len'], $mark['title'], $mark['step'], $mark['segment'], $mark['number']));
		}
		error_log(count($id_array)); 
		if (count($id_array) == 0)
			return array();
			
		if ($paginated) {
			$paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($res_id)); //$id_array));
			$paginator->setItemCountPerPage($count_for_page);
			$paginator->setCurrentPageNumber((int)$page);
	        
			$result = array('paginator' => $paginator, 'show' => array(), 'count' => count($res_id/*$id_array*/));
			if (count($id_array)) {
				$show = [];
				foreach ($paginator->getCurrentItems() as $item) {
					$show[] = $item;
				//	foreach ($item['marks'] as $mark)
				//		error_log(sprintf("%d, %d, %d, %d", $item['id'], $mark['start'], $mark['len'], $mark['title']));
				}
				$result['show'] = ArticleTables::getArticles($sm, $show, $query);//$id_array);
	        }
		}
		else {
			$result = array();
//			foreach ($id_array as $item) {
			foreach ($res_id as $item) {
				$result[] = $item['id']; //$id_array; //ArticleTables::getArticles($sm, $id_array);
			}
		}
        
		return $result;
     
	}     
    
	public static function getTutorial($sm, $word, $rule_id = 0, $paginated = false, $count_for_page = 0, $page = 0)
	{
        error_log("WordTables: getTutorial");
     //   error_log(sprintf("getArticles, word = %s, where = %d, search_part = %s", $word, $where, $search_part));
		$query = $word;
        $table = $sm->get('Contents\Model\WordTables');
        $word = trim($word, " \t.-");
        $words = explode(" ", $word);
//        $word = str_replace("-", "", $word);
//        $word = str_replace(" ", "", $word);
//        $word = str_replace(".", "", $word);

		$types = array(typeRule => array('sign' => 'rules', 
										 'join' => array(array('r' => 'rules'), 'tw.id_item = r.id', array('id_rule' => 'id')),
										 'like' => 'r.id = ',
										 'id' => 'r.id'),
					   typeFormula => array('sign' => 'formulas',
					                        'join' => array(array('f' => 'formulas'), 'tw.id_item = f.id', array('id_rule')),
					                        'like' => 'f.id_rule = ',
					                        'id' => 'f.id'),
					   typeFormulaExample => array('sign' => 'examples',
					   						       'join' => array(array('f' => 'formulas'), 'tw.id_item = f.id', array('id_rule')),
					   						       'like' => 'f.id_rule = ',
					   						       'id' => 'f.id'),
					   typeFootNote => array('sign' => 'footnotes',
					   					     'join' => array(array('f' => 'footnotes'), 'tw.id_item = f.id', array('id_rule')),
					   					     'like' => 'f.id_rule = ',
					   					     'id' => 'f.id'),
					   typeOrtho => array('sign' => 'orthos',
					   					  'join' => array(array('o' => 'orthos'), 'tw.id_item = o.id', array('id')),
					   					  'like' => 'orules.id_rule = ',
					   					  'id' => 'o.id'));
		$id_array = array();
		$id_rules = array();
		foreach ($types as $type) {
			$id_rules[$type['sign']] = array();
		}
//		'rules' => array(), 'orthos' => array(), 'formulas' => array(), 'examples' => array(), 'footnotes' => array);
		$step = 0;
		foreach ($words as &$item) {
            $item = trim($item, " \t.,:;-");
            if (strlen($item) == 0)
            	continue;
	        $aw_ON = 'w.id = tw.id';
	        //$till = $rule_id != 0 ? typeOrtho : typeRule;
			for ($type = typeRule; $type <= typeOrtho; $type++) {
		        $w_LIKE = 'tw.type='.$type.' AND '. WordTables::processSpecSym($item);//w.word LIKE \''.$item.'\'';
		        //!!!!! TMP
	//	        $w_LIKE = WordTables::processSpecSym($item, $w_LIKE);
//		print_r($id_rules);
				if ($step > 0 && count($id_rules[$types[$type]['sign']]) == 0)
					continue;
				error_log(sprintf("count of %s on step = %d = %d", $types[$type]['sign'], $step, count($id_rules[$types[$type]['sign']])));
		        if (count($id_rules[$types[$type]['sign']]) != 0) {
		        	$w_LIKE .= " AND ".$types[$type]['id']." IN (".implode(",", $id_rules[$types[$type]['sign']]).")";
		        	$id_rules[$types[$type]['sign']] = array();
		        }
		        //!!!! TMP
		        $rules = $table->tableGateway->select(function(Select $select) use ($aw_ON, $w_LIKE, $type, $rule_id, $types)
		        {
		        	$select->join(array('tw' => 'words_tutorial'), new Expression($aw_ON), array('id_item', 'start', 'len', 'type', 'number'), 'left'); 
					$select->join($types[$type]['join'][0], $types[$type]['join'][1], $types[$type]['join'][2], 'left');//array('r' => 'rules'), 'tw.id_item = r.id', 		
					if ($type == typeOrtho) {
						$select->join(array('orules' => 'orthos_rules'), 'orules.id = tw.id_item', array('id_rule'), 'left');
					}
				    if ($rule_id != 0)
						$w_LIKE = $types[$type]['like'].strval($rule_id).' AND '.$w_LIKE;

/*				    switch ($type) {
			        	case typeRule:
				            $select->join($types[$type]['join'][0], $types[$type]['join'][1], $types[$type]['join'][2], 'left');//array('r' => 'rules'), 'tw.id_item = r.id', array('id_rule' => 'id'), 'left');
					        if ($rule_id != 0)
//		    			    	$w_LIKE = 'r.id = '.strval($rule_id).' AND '.$w_LIKE;
		    			    	$w_LIKE = $types[$type]['like'].strval($rule_id).' AND '.$w_LIKE;
				            break;
			        	case typeFormula:
			        	case typeFormulaExample:
					        $select->join(array('f' => 'formulas'), 'tw.id_item = f.id', array('id_rule'), 'left');
					        if ($rule_id != 0)
		    			    	$w_LIKE = 'f.id_rule = '.strval($rule_id).' AND '.$w_LIKE;
				            break;
			        	case typeFootNote:
				            $select->join(array('f' => 'footnotes'), 'tw.id_item = f.id', array('id_rule'), 'left');
					        if ($rule_id != 0)
		    			    	$w_LIKE = 'f.id_rule = '.strval($rule_id).' AND '.$w_LIKE;
				            break;
 						case typeOrtho:
				            $select->join(array('o' => 'orthos'), 'tw.id_item = o.id', array('id'), 'left');
				            $select->join(array('orules' => 'orthos_rules'), 'orules.id = tw.id_item', array('id_rule'), 'left');
					        if ($rule_id != 0)
		    			    	$w_LIKE = 'orules.id_rule = '.strval($rule_id).' AND '.$w_LIKE;
				            break;
			        } */
			        error_log($w_LIKE);
		            $select->where($w_LIKE);
		            $select->order('tw.id_item, tw.type, tw.start');//, tw.start');
		        });
				error_log(count($rules));
	//	        $id_array = array();
		        $ruleId = 0;
		        if ($step == 0) {
			        foreach ($rules as $rule) {
			        	if (isset($rule->id_item)) {
			        		if ($ruleId != $rule->id_item) {
				        		$id_array[] = array('id' => $rule->id_item, 'marks' => array(array('start' => $rule->start, 'len' => $rule->len, 'type' => $rule->type, 'step' => $step, 'number' => $rule->number, 'rule' => $rule->id_rule)));
				        		//error_log("add to id_rules");
				        		//error_log($types[$type]['sign']);
				        		$id_rules[$types[$type]['sign']][] = $rule->id_item;
				        		//print_r($id_rules);
				        		$ruleId = $rule->id_item;
			        		}
			        		else {
			        			$add = true;
			        			// gamma-luchi & luchi
			        			foreach ($id_array[count($id_array) - 1]['marks'] as $mark) {
			        				if ($mark['start'] == $rule->start && $mark['id'] == $rule->id_item &&  $mark['type'] == $rule->type) { //$mark['rule'] == $rule->id_rule) {
			        					$add = false;
			        					break;
			        				}
			        			}
			        			if ($add === true) {
				        			$id_array[count($id_array) - 1]['marks'][] =  array('start' => $rule->start, 'len' => $rule->len, 'type' => $rule->type, 'step' => $step, 'number' => $rule->number, 'rule' => $rule->id_rule);
			        			}
			        		}
			        	}
			        }
		        }
		        else {
			        foreach ($rules as $rule) {
			        	if (isset($rule->id_item)) {
			        		foreach ($id_array as &$id) {
			        			if ($id['id'] == $rule->id_item) {
			        				$ins_idx = 0;
			        				foreach ($id['marks'] as $mark) {
			        			//print_r($mark);
			        					if ($mark['start'] > $rule->start) {
			        						break;
			        					}
										$ins_idx ++;
		
			        				}
			        				error_log(sprintf("ins_idx = %d count = %d", $ins_idx, count($id['marks'])));
			        				if ($ins_idx == count($id['marks'])) {
			        					$id['marks'][] = array('start' => $rule->start, 'len' => $rule->len, 'type' => $rule->type, 'step' => $step, 'number' => $rule->number, 'rule' => $rule->id_rule);
			        				}
			        				else {
			        					error_log(sprintf("slice ins_idx = %d", $ins_idx));
	//		        					print_r(array_slice($id['marks'], 0, $ins_idx, true));
	//		        					print_r(array_slice($id['marks'], $ins_idx, count($id['marks']) - 1, true));
									//	print_r($id['marks']);
										array_splice($id['marks'], $ins_idx, 0, array(array('start' => $rule->start, 'len' => $rule->len, 'type' => $rule->type, 'step' => $step, 'number' => $rule->number, 'rule' => $rule->id_rule)));
								//		print_r($id['marks']);
			        				}
			        				error_log(sprintf("ins_idx = %d count = %d", $ins_idx, count($id['marks'])));
			        			}
			        		}
	
			        	}
			        }
		        }
			}
			//print_r($id_rules);
			$step ++;
		}
//		print_r($id_array);
		$res_id = WordTables::fillResArray($id_array, $step, typeRule);
		$form_id = WordTables::fillResArray($id_array, $step, typeFormula);
		$ortho_id = WordTables::fillResArray($id_array, $step, typeOrtho); 
		$foot_id = WordTables::fillResArray($id_array, $step, typeFootNote); 
	/*	foreach ($res_id as &$item) {
			$tmp = $item['marks'];
			unset($item['marks']);
			$item['marks'] = array('rules' => $tmp);
		} */
/*		foreach ($res_id as &$item) {
			//$item[] = array('f' => array());
			//$item[] = array('fe' => array());
			//$item[] = array('fn' => array());
			$item[] = array('o' => array());
		} */
		foreach ($id_array as $other) {
			foreach ($other['marks'] as $mark) {
				if ($mark['type'] == typeFormula) {	
					error_log(sprintf("mark = %d, %d, %d, %d, %d, %d, id_rule = %d", $other['id'], $mark['type'], $mark['start'], $mark['len'], $mark['step'], $mark['number'], $mark['rule']));
				}
			}
		}		
		foreach ($form_id as $formula) {
			error_log(sprintf("formulas: %d", $formula['id']));
			foreach ($formula['marks'] as $mark) {
				//print_r($mark);
				error_log(sprintf("mark = %d, %d, %d, %d, %d, %d, id_rule = %d", $formula['id'], $mark['type'], $mark['start'], $mark['len'], $mark['step'], $mark['number'], $mark['rule']));
			}
			
		}
		foreach ($form_id as $other) {
			foreach ($other['marks'] as $mark) {
//				if ($mark['type'] != typeRule) {
					error_log(sprintf("%d, %d", $mark['type'], $mark['rule']));
					$res_id = WordTables::addMark($res_id, $other['id'], $mark);
//				}
			}
		}
		foreach ($foot_id as $other) {
			foreach ($other['marks'] as $mark) {
//				if ($mark['type'] != typeRule) {
					error_log(sprintf("%d, %d", $mark['type'], $mark['rule']));
					$res_id = WordTables::addMark($res_id, $other['id'], $mark);
//				}
			}
		}
		
		/*
		foreach ($id_array as $other) {
			foreach ($other['marks'] as $mark) {
				if ($mark['type'] != typeRule) {
					error_log(sprintf("%d, %d", $mark['type'], $mark['rule']));
					$res_id = WordTables::addMark($res_id, $other['id'], $mark);
				}
			}
		}
/*			foreach ($other['marks'] as $mark) {
				$found = false;
				//error_log($mark['rule']);
				foreach ($res_id as &$item) {
					if ($mark['rule'] == $item['id']) {
						$found == true;
						switch ($mark['type']) {
	//						case typeFormula:
								
						 	case typeFormulaExample:
								$item['fe'][] = $mark;
								break;
	//						case typeFootNote:
							case typeOrtho:
								error_log(sprintf("ORTHO mark = %d, rule = %d", $other['id'], $mark['rule']));
								WordTables::addMark(typeOrtho, $res_id, $other['id'], $mark);
								 								//$item['o'][] = array('id' => $other['id'], $mark;
								break;
						}
						break;
					}
				}
			}
		} */
//		error_log(sprintf("step = %d", $step));
		foreach ($res_id as $item) {
			foreach ($item['marks'] as $mark)
				error_log(sprintf("mark = %d, %d, %d, %d, %d, %d, id_rule = %d", $item['id'], $mark['type'], $mark['start'], $mark['len'], $mark['step'], $mark['number'], $mark['rule']));
			if (isset($item['foot'])) {
				foreach ($item['foot'] as $foot) {
					error_log($foot['id']);
					foreach ($foot['marks'] as $om) 
						error_log(sprintf("foot = %d, %d, %d, %d, %d, %d, id_rule = %d", $foot['id'], $om['type'], $om['start'], $om['len'], $om['step'], $om['number'], $om['rule']));
				}
			}
			if (isset($item['formula'])) {
				foreach ($item['formula'] as $formula) {
					error_log($formula['id']);
					foreach ($formula['marks'] as $om) 
						error_log(sprintf("formula = %d, %d, %d, %d, %d, %d, id_rule = %d", $formula['id'], $om['type'], $om['start'], $om['len'], $om['step'], $om['number'], $om['rule']));
				}
			}
			if (isset($item['example'])) {
				foreach ($item['example'] as $example) {
					error_log($example['id']);
					foreach ($example['marks'] as $om) 
						error_log(sprintf("example = %d, %d, %d, %d, %d, %d, id_rule = %d", $example['id'], $om['type'], $om['start'], $om['len'], $om['step'], $om['number'], $om['rule']));
				}
			}
			if (isset($item['ortho'])) {
				foreach ($item['ortho'] as $ortho) {
					error_log($ortho['id']);
					foreach ($ortho['marks'] as $om) 
						error_log(sprintf("ortho = %d, %d, %d, %d, %d, %d, id_rule = %d", $ortho['id'], $om['type'], $om['start'], $om['len'], $om['step'], $om['number'], $om['rule']));
				}
			}
			error_log("____");
		}
//print_r($res_id);
		error_log(count($res_id)); 
		if (count($res_id) == 0)
			return array();
			
		if ($paginated) {
			$paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($res_id)); //$id_array));
			$paginator->setItemCountPerPage($count_for_page);
			$paginator->setCurrentPageNumber((int)$page);
	        
			$result = array('paginator' => $paginator, 'show' => array(), 'count' => count($res_id/*$id_array*/));
			if (count($res_id)) {
				$show = [];
				foreach ($paginator->getCurrentItems() as $item) {
					$show[] = $item;
				//	foreach ($item['marks'] as $mark)
				//		error_log(sprintf("%d, %d, %d, %d", $item['id'], $mark['start'], $mark['len'], $mark['title']));
				}
				$result['show'] = RuleTables::getRules($sm, $show, $query);//$id_array);
	        }
		}
		else {
			$result = $res_id; //array();
//			foreach ($id_array as $item) {
//			foreach ($res_id as $item) {
//				$result[] = $item; //$id_array; //ArticleTables::getArticles($sm, $id_array);
//			}
		}
        
        return $result;
     
	}     
          
 } 
 
 ?>
 
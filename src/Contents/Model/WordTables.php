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


 class WordTables
 {
    protected $tableGateway;
	
	const typeTitle = 1;
	const typeText = 2;
	 //
	const typeRule = 4;
	const typeFormula = 5;
	const typeFormulaExample = 6;
	const typeFootNote = 7;
	const typeOrtho = 8;
    
    static $types = array(self::typeRule => array('sign' => 'rules', 
										 'join' => array(array('r' => 'rules'), 'tw.id_item = r.id', array('id_rule' => 'id')),
										 'like' => 'r.id = ',
										 'id' => 'r.id'),
					   	  self::typeFormula => array('sign' => 'formulas',
					                        'join' => array(array('f' => 'formulas'), 'tw.id_item = f.id', array('id_rule')),
					                        'like' => 'f.id_rule = ',
					                        'id' => 'f.id'),
					      self::typeFormulaExample => array('sign' => 'examples',
					   						       'join' => array(array('f' => 'formulas'), 'tw.id_item = f.id', array('id_rule')),
					   						       'like' => 'f.id_rule = ',
					   						       'id' => 'f.id'),
					      self::typeFootNote => array('sign' => 'footnotes',
					   					     'join' => array(array('f' => 'footnotes'), 'tw.id_item = f.id', array('id_rule')),
					   					     'like' => 'f.id_rule = ',
					   					     'id' => 'f.id'),
					      self::typeOrtho => array('sign' => 'orthos',
					   						 'join' => array(array('o' => 'orthos'), 'tw.id_item = o.id', array('id')),
					   						 'like' => 'orules.id_rule = ',
					   						 'id' => 'o.id'));



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
					$LIKE = 'w.word REGEXP \''.substr($query, 1, strlen($query) - 2).'\'';
				}
				else if (strpos($query, "*") == 0) {
					$LIKE = 'w.word REGEXP \''.substr($query, 1).'$\'';
				}
				else if (strrpos($query, "*") == strlen($query) - 1) {
					$LIKE = 'w.word REGEXP \'^'.substr($query, 0, strlen($query) - 1).'\'';
				}
				$LIKE = str_replace("*", "[[:alpha:]{0,2}|[:punct:]|[:space:]|-]*", $LIKE);
			}		        
			if (strpos($query, "?") !== false) {
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
//		print_r($id_array);
/*		foreach ($id_array as $item) {
			if (count($item['marks']) < $step)
				continue;
			error_log(sprintf("id = %d", $item['id']));
			foreach ($item['marks'] as $mark) {
				if (isset($mark['segment'])) {
					error_log(sprintf("start = %d, len = %d, step = %d, segment = %d, number = %d", $mark['start'], $mark['len'], $mark['step'], $mark['segment'], $mark['number']));
				}
				else {
					error_log(sprintf("start = %d, len = %d, step = %d, number = %d", $mark['start'], $mark['len'], $mark['step'], $mark['number']));
				}
			}
		} */
		
		$res_id = array();
		//error_log(sprintf("step = %d", $step));
		foreach ($id_array as &$item) {
			if (count($item['marks']) < $step)
				continue;
			$start = 0;
			foreach ($item['marks'] as &$mark) {
				if ($type !== false && $mark['type'] != $type)
					continue;
				//error_log(sprintf("mark[step] = %d", $mark['step']));
				if ($mark['step'] == 0) { // find mark with step = 0
//					$segment = $mark['segment'];
					$number = $mark['number'];
					for ($i = $start + 1, $j = 1; $i < count($item['marks'])/* && $j < $step*/; $i++/*, $j++*/) {
	//					error_log($end);
	//					error_log($item['marks'][$i]['start'] - $end);
//						error_log($item['marks'][$i]['len']);
//						error_log(sprintf("id = %d, %d, %d, %d", $item['id'], $item['marks'][$i]['step'], $item['marks'][$i]['number'], $item['marks'][$i]['start']));
//						error_log($item['marks'][$i]['number']);
						if ($item['marks'][$i]['step'] != $j) { // || $item['marks'][$i]['start'] - $end > $item['marks'][$i]['len']) {
//							error_log("break step");
							//break;
							continue;
						}
						if ($item['marks'][$i]['number'] != $number + 1) {
							//break;
							//
							continue;
						}
						$number++;
						$j++;
					}
					if ($j == $step) {
						if (count($res_id) == 0) {
							//error_log("fill res_id");
							$res_id[] = array('id' => $item['id'], 'marks' => array());
							for ($i = $start, $j = 0; $i < count($item['marks']) && $j < $step; $i++) {
								if ($item['marks'][$i]['step'] != $j)
									continue;
								$res_id[count($res_id) - 1]['marks'][] = $item['marks'][$i];
								$j++;
								//error_log(sprintf("i = %d", $i));
							}
						}
						else {
							//error_log("fill res_id 1");
							$done = false;
							foreach ($res_id as &$res_item) {
								if ($res_item['id'] == $item['id']) {
									for ($i = $start, $j = 0; $i < count($item['marks']) && $j < $step; $i++) {
										if ($item['marks'][$i]['step'] != $j)
											continue;
										$res_item['marks'][] = $item['marks'][$i];
										$j++;
									}
									$done = true;
								}
							}
							if ($done === false) {
								$res_id[] = array('id' => $item['id'], 'marks' => array());
								for ($i = $start, $j = 0; $i < count($item['marks']) && $j < $step; $i++) {
									if ($item['marks'][$i]['step'] != $j)
										continue;
									$res_id[count($res_id) - 1]['marks'][] = $item['marks'][$i];
									$j++;
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
		$sign = @self::$types[$mark['type']]['sign'];
		$found = false;
		foreach ($res_id as &$item) {
			if ($item['id'] == $mark['rule']) {
				$found = true;
//				error_log(sprintf("rule %d found ", $mark['rule']));
					//error_log($sign);
				if (!isset($item[$sign])) {
					$item[$sign] = array(array('id' => $other_id, 'marks' => array($mark)));
				}
				else {
					$found_spec = false;
					foreach ($item[$sign] as &$spec) {
						if ($spec['id'] == $other_id) {
							$found_spec = true;
							$spec['marks'][] = $mark;
							//error_log("add spec");
							break;
						}
					}
					if ($found_spec === false) {
						//error_log("add spec 1");
						$item[$sign][] = array('id' => $other_id, 'marks' => array($mark));
					}
				}
				break;
			}
		}
		//print_r($res_id);
		if ($found === false && $mark['rule'] != 0) {
			if (count($res_id) > 0) {
				$idx = 0;
				foreach ($res_id as &$item) {
					if (intval($mark['rule']) < intval($item['id'])) {
						//error_log(sprintf("add rule to res-id = %d idx = %d", $mark['rule'], $idx));
						//error_log(count($res_id));
							array_splice($res_id, $idx, 0, array(array('id' => strval($mark['rule']), 'marks' => array(), $sign => array(array('id' => strval($other_id), 'marks' => array($mark))))));
						//error_log(count($res_id));
						break;
					}
					$idx++;
				}
				if ($idx == count($res_id)) {
					$res_id[] = array('id' => strval($mark['rule']), 'marks' => array(), $sign => array(array('id' => strval($other_id), 'marks' => array($mark))));
				}
			}
			else {
				//error_log("add to empty");
				$res_id = array(array('id' => strval($mark['rule']), 'marks' => array(), $sign => array(array('id' => strval($other_id), 'marks' => array($mark)))));
					
			}
		}
		return $res_id;
		
	}
	
	public static function emptyResult($paginated, $count_for_page, $page)
	{
		if ($paginated) {
			$paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter(array()));
			$paginator->setItemCountPerPage($count_for_page);
			$paginator->setCurrentPageNumber((int)$page);
		        
			$result = array('paginator' => $paginator, 'show' => array(), 'count' => 0);
		}
		else {
			$result = array();
		}
		return $result;
		
	}
	
	public static function checkQuery($word)
	{
		$word = preg_replace("/[\[|\]|\{|\}|\d|\$|\%|\^|\&|\(|\)|\!|\:|\;|\#]/", "", $word);
		$word = preg_replace("/[\*]+/","*",$word);
       	$words = explode(" ", $word);
       	foreach ($words as $item) {
       		if (strpos($item, "*") !== false) {
       			$tmp = preg_replace("/[\*]/","",$item);
       			if (mb_strlen($tmp, 'UTF-8') < 2) {
       				return array();
       			}
       		}
//       		error_log($item);
       	}
       	return $words;
	}
	
    public static function getArticles($sm, $word, $where = 3, $paginated = false, $count_for_page = 0, $page = 0)
    {
		error_log("WordTables: getArticles");
		error_log(sprintf("word = %s", $word));
		if ($where == 0) {
			return WordTables::emptyResult($paginated, $count_for_page, $page);
		}
     //   error_log(sprintf("getArticles, word = %s, where = %d, search_part = %s", $word, $where, $search_part));
       	$query = $word;
       	$table = $sm->get('Contents\Model\WordTables');
       	$word = trim($word, " \t.-");
       	$words = WordTables::checkQuery($word);
       	if (count($words) == 0) {
       		return WordTables::emptyResult($paginated, $count_for_page, $page);
       	}
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

	        if (count($id_arts) != 0) {
	        	$w_LIKE .= " AND a.id IN (".implode(",", $id_arts).")"; 
	        }
	        //error_log(sprintf("step = %d: %s", $step, $w_LIKE));
	        $articles = $table->tableGateway->select(function(Select $select) use ($aw_ON, $w_LIKE)
	        {
	        	$select->join(array('aw' => 'words_articles'), new Expression($aw_ON), array('id_article', 'start', 'len', 'title', 'segment', 'number'), 'left'); 
	            $select->join(array('a' => 'articles'), 'aw.id_article = a.id', array('id'), 'left');
	            $select->where($w_LIKE);
	            $select->order('aw.title, aw.id_article, aw.start');
	        });
//			error_log(count($articles));
//	        $id_array = array();
	        $artId = 0;
	        $id_arts = array();
	        if ($step == 0) {
		        foreach ($articles as $article) {
		        	if (isset($article->id_article)) {
		        		$space = strpos($article->word, " ") === false ? 0 : 1;
		        		//error_log(sprintf("id_article = %d", $article->id_article));
		        		if ($artId != $article->id_article) {
			        		$id_array[] = array('id' => $article->id_article, 'marks' => array(array('start' => $article->start, 'len' => $article->len, 'title' => $article->title, 'step' => $step, 'segment' => $article->segment, 'number' => $article->number, 'space' => $space)));
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
			        			$id_array[count($id_array) - 1]['marks'][] =  array('start' => $article->start, 'len' => $article->len, 'title' => $article->title, 'step' => $step, 'segment' => $article->segment, 'number' => $article->number, 'space' => $space);
		        			}
		        		}
		        	}
		        }
	        }
	        else {
		        foreach ($articles as $article) {
		        	if (isset($article->id_article)) {
		        		$space = strpos($article->word, " ") === false ? 0 : 1;
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
		//        				error_log(sprintf("ins_idx = %d count = %d", $ins_idx, count($id['marks'])));
		        				if ($ins_idx == count($id['marks'])) {
		        					$id['marks'][] = array('start' => $article->start, 'len' => $article->len, 'title' => $article->title, 'step' => $step, 'segment' => $article->segment, 'number' => $article->number, 'space' => $space);
		        				}
		        				else {
		//        					error_log(sprintf("slice ins_idx = %d", $ins_idx));
//		        					print_r(array_slice($id['marks'], 0, $ins_idx, true));
//		        					print_r(array_slice($id['marks'], $ins_idx, count($id['marks']) - 1, true));
								//	print_r($id['marks']);
									array_splice($id['marks'], $ins_idx, 0, array(array('start' => $article->start, 'len' => $article->len, 'title' => $article->title, 'step' => $step, 'segment' => $article->segment, 'number' => $article->number, 'space' => $space)));
							//		print_r($id['marks']);
		        				}
		        				if (count($id_arts) == 0 || $id_arts[count($id_arts) - 1] != $article->id_article)
			        				$id_arts[] = $article->id_article;
		//        				error_log(sprintf("ins_idx = %d count = %d", $ins_idx, count($id['marks'])));
		        			}
		        		}

		        	}
		        }
	        }
	        $step ++;
		}
	//	error_log($id_array[0]['id']);
		$res_id = WordTables::fillResArray($id_array, $step);
//		error_log(sprintf("step = %d", $step));

//print_r($res_id);
	/*	foreach ($res_id as $item) {
			foreach ($item['marks'] as $mark)
				error_log(sprintf("mark = %d, %d, %d, %d, %d, %d, %d", $item['id'], $mark['start'], $mark['len'], $mark['title'], $mark['step'], $mark['segment'], $mark['number']));
		} */
	//	error_log(count($id_array)); 
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
			foreach ($res_id as $item) {
				$result[] = $item['id']; 
			}
		}
        
		return $result;
     
	}     
    
    public static function getRelArticles($sm, $word, $where = 3, $paginated = false, $count_for_page = 0, $page = 0)
    {
    	
    }
    
	public static function getTutorial($sm, $word, $check = 1, $rule_id = 0, $paginated = false, $count_for_page = 0, $page = 0)
	{
        error_log("WordTables: getTutorial");
		error_log(sprintf("word = %s", $word));
        
        if ($check == 0) {
			return WordTables::emptyResult($paginated, $count_for_page, $page);
        }
        
		$query = $word;
        $table = $sm->get('Contents\Model\WordTables');
        $word = trim($word, " \t.-");

       	$words = WordTables::checkQuery($word);
       	if (count($words) == 0) {
       		return WordTables::emptyResult($paginated, $count_for_page, $page);
       	}
        
//        $words = explode(" ", $word);
		
		$types = @self::$types;					   					  
		
		$id_array = array();
		$id_rules = array();
		foreach ($types as $type) {
			$id_rules[$type['sign']] = array();
		}
		$step = 0;
		foreach ($words as &$item) {
            $item = trim($item, " \t.,:;-");
            if (strlen($item) == 0)
            	continue;
	        $aw_ON = 'w.id = tw.id';
			for ($type = self::typeRule; $type <= self::typeOrtho; $type++) {
//	        	error_log($type);
		        $w_LIKE = 'tw.type='.$type.' AND '. WordTables::processSpecSym($item);//w.word LIKE \''.$item.'\'';
				if ($step > 0 && count($id_rules[$types[$type]['sign']]) == 0) {
					continue;
				}
//				error_log(sprintf("count of %s on step = %d = %d", $types[$type]['sign'], $step, count($id_rules[$types[$type]['sign']])));
		        if (count($id_rules[$types[$type]['sign']]) != 0) {
		        	$w_LIKE .= " AND ".$types[$type]['id']." IN (".implode(",", $id_rules[$types[$type]['sign']]).")";
		        	$id_rules[$types[$type]['sign']] = array();
		        }
		        $rules = $table->tableGateway->select(function(Select $select) use ($aw_ON, $w_LIKE, $type, $rule_id, $types)
		        {
		        	$select->join(array('tw' => 'words_tutorial'), new Expression($aw_ON), array('id_item', 'start', 'len', 'type', 'number'), 'left'); 
					$select->join($types[$type]['join'][0], $types[$type]['join'][1], $types[$type]['join'][2], 'left');
					if ($type == self::typeOrtho) {
						$select->join(array('orules' => 'orthos_rules'), 'orules.id = tw.id_item', array('id_rule'), 'left');
					}
				    if ($rule_id != 0)
						$w_LIKE = $types[$type]['like'].strval($rule_id).' AND '.$w_LIKE;

			        //error_log($w_LIKE);
		            $select->where($w_LIKE);
		            $select->order('tw.id_item, tw.type, tw.start');
		        });
				//error_log(count($rules));
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
			        				if ($mark['start'] == $rule->start && $id_array[count($id_array) - 1]['id'] == $rule->id_item &&  $mark['type'] == $rule->type) { //$mark['rule'] == $rule->id_rule) {
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
									$id_rules[$types[$type]['sign']][] = $rule->id_item;
			        				$ins_idx = 0;
			        				foreach ($id['marks'] as $mark) {
			        					if ($mark['start'] > $rule->start) {
			        						break;
			        					}
										$ins_idx ++;
		
			        				}
			        				//error_log(sprintf("ins_idx = %d count = %d", $ins_idx, count($id['marks'])));
			        				if ($ins_idx == count($id['marks'])) {
			        					$id['marks'][] = array('start' => $rule->start, 'len' => $rule->len, 'type' => $rule->type, 'step' => $step, 'number' => $rule->number, 'rule' => $rule->id_rule);
			        				}
			        				else {
//			        					error_log(sprintf("slice ins_idx = %d", $ins_idx));
										array_splice($id['marks'], $ins_idx, 0, array(array('start' => $rule->start, 'len' => $rule->len, 'type' => $rule->type, 'step' => $step, 'number' => $rule->number, 'rule' => $rule->id_rule)));
			        				}
			        				//error_log(sprintf("ins_idx = %d count = %d", $ins_idx, count($id['marks'])));
			        			}
			        		}
	
			        	}
			        }
		        }
			}
			$step ++;
		}
		$res_id = WordTables::fillResArray($id_array, $step, self::typeRule);
		$form_id = WordTables::fillResArray($id_array, $step, self::typeFormula);
		//print_r($form_id);
		$ex_id = WordTables::fillResArray($id_array, $step, self::typeFormulaExample);
		$ortho_id = WordTables::fillResArray($id_array, $step, self::typeOrtho); 
		$foot_id = WordTables::fillResArray($id_array, $step, self::typeFootNote); 

		foreach ($form_id as $other) {
			foreach ($other['marks'] as $mark) {
				//error_log(sprintf("%d, %d, %d", $mark['type'], $mark['rule'], $other['id']));
				$res_id = WordTables::addMark($res_id, $other['id'], $mark);
			}
		}
		//print_r($res_id);
		foreach ($ex_id as $other) {
			foreach ($other['marks'] as $mark) {
				//error_log(sprintf("add example %d, %d", $mark['type'], $mark['rule']));
				$res_id = WordTables::addMark($res_id, $other['id'], $mark);
			}
		}
		foreach ($foot_id as $other) {
			foreach ($other['marks'] as $mark) {
//				error_log(sprintf("%d, %d", $mark['type'], $mark['rule']));
				$res_id = WordTables::addMark($res_id, $other['id'], $mark);
			}
		}
		foreach ($ortho_id as $other) {
			foreach ($other['marks'] as $mark) {
//				error_log(sprintf("%d, %d", $mark['type'], $mark['rule']));
				$res_id = WordTables::addMark($res_id, $other['id'], $mark);
			}
		}
		if (count($res_id) == 0)
			return array();
		
		//WordTables::logResID($res_id);
		
		if ($paginated) {
			$paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($res_id));
			$paginator->setItemCountPerPage($count_for_page);
			$paginator->setCurrentPageNumber((int)$page);
	        
			$result = array('paginator' => $paginator, 'show' => array(), 'count' => count($res_id));
			if (count($res_id)) {
				$show = [];
				foreach ($paginator->getCurrentItems() as $item) {
					$show[] = $item;
				/*	foreach ($item['marks'] as $mark)
						error_log(sprintf("%d, %d, %d, %d", $item['id'], $mark['start'], $mark['len'], $mark['type'])); */
				}
				$result['show'] = RuleTables::getRules($sm, $show, $query);
	        }
		}
		else {
			$result = $res_id;
		}
        
        return $result;
     
	}
	
	static function logResID($res_id)
	{
		foreach ($res_id as $item) {
			error_log($item['id']);
		}		
		foreach ($res_id as $item) {
			foreach ($item['marks'] as $mark)
				error_log(sprintf("mark = %d, %d, %d, %d, %d, %d, id_rule = %d", $item['id'], $mark['type'], $mark['start'], $mark['len'], $mark['step'], $mark['number'], $mark['rule']));
			for ($i = self::typeFormula; $i <= self::typeOrtho; $i++) {
				if (isset($item[@self::$types[$i]['sign']])) {
					foreach ($item[@self::$types[$i]['sign']] as $subitem) {
						error_log($subitem['id']);
						foreach ($subitem['marks'] as $m) 
							error_log(sprintf("%s = %d, %d, %d, %d, %d, %d, id_rule = %d", @self::$types[$i]['sign'], $item['id'], $m['type'], $m['start'], $m['len'], $m['step'], $m['number'], $m['rule']));
					}
				}
			}
			error_log("____"); 
		}
		error_log(count($res_id)); 
	}   
          
 } 
 
 ?>
 
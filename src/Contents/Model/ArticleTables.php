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
 use Zend\Db\ResultSet\ResultSet;
 use Zend\Db\Sql\Select;
 use Zend\Db\Sql\Delete;
 use Zend\Db\Sql\Sql;
 use Zend\Session\SessionManager;

 class ArticleTables
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

    public static function getParaForArticle($sm, $id)
    {
    }

	public static function getSrc($sm, $id)
	{
        $table = $sm->get('Contents\Model\ArticleTables');
        $articles = $table->tableGateway->select(function(Select $select) use ($id)
        {
            $select->where('a.id = '.strval($id));
        });
        if ($articles->count())
            return ($articles->current()->src); 
        return false;
	}
    
    public static function getText($sm, $id)
	{
        $table = $sm->get('Contents\Model\ArticleTables');
        $articles = $table->tableGateway->select(function(Select $select) use ($id)
        {
            $select->where('a.id = '.strval($id));
        });
        if ($articles->count())
            return ($articles->current()->text); 
        return false;
	}
	
	public static function getTitle($sm, $id)
	{
        $table = $sm->get('Contents\Model\ArticleTables');
        $articles = $table->tableGateway->select(function(Select $select) use ($id)
        {
            $select->where('a.id = '.strval($id));
        });
        if ($articles->count()) {
            return ($articles->current()->title[0]);
            //$t = explode(";", $str);
            //return $t[0]; 
        }
	}

	public static function getByTitle($sm, $title)
	{
        $table = $sm->get('Contents\Model\ArticleTables');
        $articles = $table->tableGateway->select(function(Select $select) use ($title)
        {
            //print_r('(a.title = '.$title.' AND a.dic=49)');
            $select->where('(a.title = \''.$title.'\')');
        });
        //print_r($articles);
        if ($articles->count())
            return ($articles->current()->dic); 
        return false;
	}

    public static function getCountForFormula($sm, $id_formula)
    {
        $table = $sm->get('Contents\Model\ArticleTables');
        $articles = $table->tableGateway->select(function(Select $select) use ($id_formula)
        {
            $select->where('a.id = '.strval($id));
        });
        if ($articles->count())
            return ($articles->current()->text); 
        return false;
    }

    public static function getArticlesForFormula($sm, $id_formula)
    {
        $table = $sm->get('Contents\Model\ArticleTables');
        $articles = $table->tableGateway->select(function(Select $select) use ($id_formula)
        {
            $select->where('a.id = '.strval($id));
        });
        if ($articles->count())
            return ($articles->current()->text); 
    }

    public static function deleteArticle($sm, $id)
    {
        // delete links
        WordTables::deleteArticle($sm, $id);
        BigrammTables::deleteArticle($sm, $id);
        TrigrammTables::deleteArticle($sm, $id);
        TetragrammTables::deleteArticle($sm, $id);
        // delete article
		$action = new Delete('articles');
        $action->where(array('id = ?' => $id));

         $sql    = new Sql($sm->get('Zend\Db\Adapter\Adapter'));
         $stmt   = $sql->prepareStatementForSqlObject($action);
         $result = $stmt->execute();		
    }

    public static function getArticlesForRule($sm, $id_rule)
    {
        error_log("getArticlesForRule");
        $table = $sm->get('Contents\Model\ArticleTables');
        $articles = $table->tableGateway->select(function(Select $select) use ($id_rule)
        {
            $select->join(array('ar' => 'articles_rules'), 'a.id = ar.id', array('id'), 'left'); 
            $select->where('ar.id_rule = '.strval($id_rule));
        });
        $ids = array();
        if ($articles->count()) {
            error_log(count($articles));
            foreach ($articles as $article) {
                $ids[] = $article->id;
            }
        }
        return $ids;
    }
     
	public static function isFirst($title, $query)
    {
		$query = trim($query, " \t.,:;-");    	
		foreach ( $title as $item)
		{	
		    #error_log($item);
        	if (strlen($item) == strlen($query) && $item == $query) {
        	   # error_log("ok");
        		return true;
    	    }
    	    else if (strlen($item) > strlen($query)) {
    		    $head = substr($item, 0, strlen($query));
//    		error_log(sprintf("query = %s len = %d", $query, strlen($query)));
    		//error_log(sprintf("head  = %s", $head));
    		    if ($head == $query) {
	    		    $tail = substr($item, strlen($query));
//	    		error_log(sprintf("tail = %s", $tail));
				    if (preg_match('/^[^\p{L}]+$/u', $tail)) {
					    return true;
					}
    		    }
    		}
    	}
    	return false;
    }
     
	public static function getArticles($sm, $id_array, $query = null)
    {
        error_log("getArticles for id_array");
        $table = $sm->get('Contents\Model\ArticleTables');
		$result = array();
        
        // 0 - title
        // 1 - article
		$id_all = array();
		foreach ($id_array as $item) {
			$id_all[] = $item['id'];
		}
		$id_title = array();
		foreach ($id_array as $item) {
			foreach ($item['marks'] as $mark)
				if ($mark['title'] == 1) {
//				    error_log(sprintf("title %d", $item['id']));
					$id_title[] = $item['id'];
				    break;
			}
		}
		$id_arts = array_diff($id_all, $id_title);		

		$mark_before = "<span class=\"marked\" >";
		$mark_after = "</span>";
		$mark_len = strlen($mark_before) + strlen($mark_after);
		$class_title = "class=\"title";
		$mark_class_title = " marked";
		$mark_class_len = strlen($mark_class_title);
		
		for ($i = 0; $i < 2; $i++) {
			if ($i == 0 && count($id_title) > 0)
				$ids = implode(",", $id_title);//$id_array);
			else if ($i == 1 && count($id_arts) > 0)
				$ids = implode(",", $id_arts);//$id_array);
			else 
				continue;
//			error_log($i);
//			error_log($ids);
//$ids = '161221';
	        $articles = $table->tableGateway->select(function(Select $select) use ($ids)
	        {
	            $select->join(array('ap' => 'articles_paras'), 'a.id = ap.id', array('id_para'), 'left');
	            $select->join(array('ar' => 'articles_rules'), 'a.id = ar.id', array('id_rule'), 'left');
	            $select->join(array('ao' => 'articles_orthos'), 'a.id = ao.id', array('id_ortho'), 'left'); 
	//            $select->join(array('af' => 'articles_formulas'), 'a.id = af.id', array('id_formula'), 'left'); 
	            $select->join(array('ac' => 'articles_comments'), 'a.id = ac.id', array('id_comment'), 'left'); 
	            $select->join(array('ai' => 'articles_addinfo'), 'a.id = ai.id_article', array('id_addinfo' => 'id', 'id_src' => 'id_src', 'addinfo' => 'text'), 'left'); 
	            $select->where('a.id IN ('.$ids.')');
	            $select->order('a.id');
	        }); 
	        
	        $artId = 0;
	        $first = false;
	        $firstId = -1;
	        foreach ($articles as $article)
	        {
	            error_log(sprintf("%d, %d", $article->id, $artId));
	            if ($artId != $article->id) {
					$offset = 0;
					if ($i == 0)
						$first = ArticleTables::isFirst($article->title, $query);
					if ($first === true)
						$firstId += 1;
					foreach ($id_array as $item) {
						if ($article->id == $item['id']) {
							$prev = 0;
							$prev_len = 0;
							if ($i == 0) {
								$offset = 0;
								$pos = strpos($article->text, $mark_before);
								$title_m = false;
								if ($pos == false) {
									$pos = strpos($article->text, $mark_class_title);
									$title_m = true;
								}
								//error_log(sprintf("pos mark-before = %d", $pos));
								while ($pos !== false && $pos - $offset < $item['marks'][0]['start']) {
									if ($title_m) {
										$title_m = false;
										$offset += $mark_class_len;
									}
									else
										$offset += $mark_len;
									$pos = strpos($article->text, $mark_before, $pos + $offset);
									//error_log(sprintf("pos mark-before = %d", $pos));
								}
							}														
							//$prev_segment = 0;
//                            $k=0;
							foreach ($item['marks'] as $mark_word) {
/*							    $k++;
							    error_log($k);
							    if ($k != 2)
							        continue; */
							       error_log(sprintf("marks n = %d, start = %d", $k, $mark_word['start']));
								if ($mark_word['start'] != 0 && $mark_word['step'] != -1) {
						//			error_log($article->text);
	//								error_log(strpos($article->text, "<span"));
	//								error_log($item['start']);
						//			error_log("!!!222");
						//			error_log(sprintf("id = %d, start = %d, len = %d, step = %d, space = %d", $item['id'], $mark_word['start'], $mark_word['len'], $mark_word['step'], $mark_word['space']));
									if (/*$prev_segment == $mark_word['segment'] && */$mark_word['start'] + $mark_word['len'] <= $prev + $prev_len)
										continue;
									$prev = $mark_word['start'];
									$prev_len = $mark_word['len'];
									$prev_segment = $mark_word['segment'];
									//    error_log(sprintf("222mark_word start %s offset %d",$mark_word['start'],$offset));
									$article->text = substr_replace($article->text, $mark_before, $mark_word['start'] + $offset, 0);
									$article->text = substr_replace($article->text, $mark_after, $mark_word['start'] + $mark_word['len'] + $offset + strlen($mark_before), 0);
									$offset += $mark_len;
									//	break;
					//					error_log($article->text);
								//	error_log($article->text);
								}
							}
						}
					}
					if ($first === true) {
						if ($firstId == count($result))
			                $result[] = array('article' => $article->text, 'id' => $article->id, 'dic' => $article->dic, 'paras' => array(), 'rules' => array(), 'orthos' => array(), 'comments' => array(), 'addinfo' => array());
			            else {
//							error_log(sprintf("firstId before slice %d", $firstId));
							array_splice($result, $firstId, 0, array(array('article' => $article->text, 'id' => $article->id, 'dic' => $article->dic, 'paras' => array(), 'rules' => array(), 'orthos' => array(), 'comments' => array(), 'addinfo' => array())));
			            } 
					}
						
					else {
		                $result[] = array('article' => $article->text, 'id' => $article->id, 'dic' => $article->dic, 'paras' => array(), 'rules' => array(), 'orthos' => array(), 'comments' => array(), 'addinfo' => array());
					}
	                $artId = $article->id;
	            }

				$idx = count($result) - 1;
				if ($first === true)
					$idx = $firstId;
				//error_log(sprintf("idx = %d", $idx));	
	            if ($article->id_para != null && array_key_exists(strval($article->id_para), $result[$idx]['paras']) == false)  {
	            	//error_log(sprintf("add para %d to article %d", $artId, $article->id_para));
					$title = ParagraphTables::getParaTitle($sm, $article->id_para);
	            	$result[$idx]['paras'][strval($article->id_para)] = array('title' => $title, 'act_rules' => array());
	            }
				if ($article->id_rule != null) {
					$para = RuleTables::getPara($sm, $article->id_rule);
					if ($para !== false) {
						if (array_key_exists(strval($para), $result[$idx]['paras']) == false) {
							$title = ParagraphTables::getParaTitle($sm, $para);
							$result[$idx]['paras'][strval($para)] = array('title' => $title, 'act_rules' => array());
						}
						if (array_key_exists(strval($article->id_rule), $result[$idx]['paras'][strval($para)]['act_rules']) == false) {
							$result[$idx]['paras'][strval($para)]['act_rules'][strval($article->id_rule)] = RuleTables::getRuleNum($sm, $article->id_rule);
						}
					}
				}
		        if ($article->id_ortho != null && array_key_exists(strval($article->id_ortho), $result[$idx]['orthos']) == false) {
					$ortho = OrthogrTables::getOrthogr($sm, $article->id_ortho);
		            $result[$idx]['orthos'][strval($article->id_ortho)] = array('name' => $ortho);
		        } 
		        
		        if ($article->id_comment != null && array_key_exists(strval($article->id_comment), $result[$idx]['comments']) == false) {
	//				$ortho = OrthogrTables::getOrthogr($sm, $article->id_ortho);
		            $result[$idx]['comments'][strval($article->id_comment)] = array('title' => ArticleTables::getTitle($sm, $article->id_comment));
		        }
		        if ($article->id_addinfo != null && array_key_exists(strval($article->id_addinfo), $result[$idx]['addinfo']) == false) {
		            $result[$idx]['addinfo'][strval($article->id_addinfo)] = array('src' => SourcesTable::getSrcById($sm, $article->id_src));
		        } 
	        }
        }
      //  error_log(sprintf("result count = %d", count($result)));
        return $result;
	}
     
	public static function putArticlesToRtf($sm, $id_array, $word, $formula, $ortho)
	{		
        //error_log("getArticles for id_array"); 
        $table = $sm->get('Contents\Model\ArticleTables');
        $articles = $table->tableGateway->select(function(Select $select) use ($id_array)
        {
            $select->where('a.id IN ('.$id_array.')');
            $select->order('a.id');
        });
        
        // save result to rtf.
        $filename = 'articles'.uniqid().'.rtf';
		$fp = fopen('downloads/'.$filename, 'w');
	//	$ft = fopen('downloads/'.$filename.".txt", 'w');
		if ($fp) {
			fwrite($fp, "{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\froman\\fprq2\\fcharset204{\\*\\fname Times New Roman;}Times New Roman CYR;}{\\f1\\froman\\fprq2\\fcharset204 Times Roman Cyr Acsent;}}");
	//		$idx = 1;
			if ($formula != null) {
				fwrite($fp, "\\par".FormulaTables::getFRTF($sm, $formula));
				fwrite($fp, "\\par");
			}
			else if ($ortho != null) {
				fwrite($fp, "\\par".OrthogrTables::getFRTF($sm, $ortho));
				fwrite($fp, "\\par");
			}
	        foreach ($articles as $article)
	        {
/*	            $posb = strpos($article->src, "</b>");
	            $posa = strpos($article->src, "&#x301");
	            if ($posa === false || $posa > $posb) {
//	                fwrite($ft, $article->id."\t".$article->title."\n");
	                fwrite($ft, $article->src."\n"); */
    				fwrite($fp, "\\par".$article->rtf);
/*    				$idx++;
    			} */
	        }
    //        error_log(sprintf("rtf without accent %d", $idx));
	        //print_r($result);
	        fwrite($fp, "}");
			fclose($fp);
	//		fclose($ft);
			return $filename;
		}
		return false;
	}
 } 
 
 ?>
 
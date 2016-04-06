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

	public static function getTitle($sm, $id)
	{
        $table = $sm->get('Contents\Model\ArticleTables');
        $articles = $table->tableGateway->select(function(Select $select) use ($id)
        {
            $select->where('a.id = '.strval($id));
        });
        if ($articles->count())
            return ($articles->current()->title); 
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
    
     public static function getArticles($sm, $id_array, $paginated = false)
     {
        //error_log("getArticles for id_array");
        $table = $sm->get('Contents\Model\ArticleTables');
		$ids = implode(",", $id_array);
        $articles = $table->tableGateway->select(function(Select $select) use ($ids)
        {
            $select->join(array('ap' => 'articles_paras'), 'a.id = ap.id', array('id_para'), 'left');
            $select->join(array('ar' => 'articles_rules'), 'a.id = ar.id', array('id_rule'), 'left');
            $select->join(array('ao' => 'articles_orthos'), 'a.id = ao.id', array('id_ortho'), 'left'); 
            $select->join(array('af' => 'articles_formulas'), 'a.id = af.id', array('id_formula'), 'left'); 
            $select->where('a.id IN ('.$ids.')');
            $select->order('a.id');
        });
        
//        error_log(count($articles));
        $artId = 0;
        $result = array();
        // save result to rtf.
//		$fp = fopen('articles.rtf', 'w');
//		fwrite($fp, "{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\froman\\fprq2\\fcharset204{\\*\\fname Times New Roman;}Times New Roman CYR;}{\\f1\\froman\\fprq2\\fcharset204 Times Roman Cyr Acsent;}}");
        foreach ($articles as $article)
        {
            //error_log(sprintf("%d, %d", $article->id, $artId));
            if ($artId != $article->id) {
                $result[] = array('article' => $article->text, 'id' => $article->id, 'paras' => array(), 'rules' => array(), 'orthos' => array(), 'comment' => $article->id_comment != 0 ? array('comment_id' => $article->id_comment, 'title' => ArticleTables::getTitle($sm, $article->id_comment)) : array());
                $artId = $article->id;
      //          fwrite($fp, "\\par".$article->rtf);
            }
            if ($article->id_para != null && array_key_exists(strval($article->id_para), $result[count($result) - 1]['paras']) == false)  {
            	//error_log(sprintf("add para %d to article %d", $artId, $article->id_para));
				$title = ParagraphTables::getParaTitle($sm, $article->id_para);
				/*$rules = RuleTables::getRulesForPara($sm, $article->id_para); 
            	$result[count($result) - 1]['paras'][strval($article->id_para)] = array('title' => $title, 'rules' => $rules, 'act_rules' => array()); */
            	$result[count($result) - 1]['paras'][strval($article->id_para)] = array('title' => $title, 'act_rules' => array());
            }
			if ($article->id_rule != null) {
				$para = RuleTables::getPara($sm, $article->id_rule);
				if ($para !== false) {
					if (array_key_exists(strval($para), $result[count($result) - 1]['paras']) == false) {
						$title = ParagraphTables::getParaTitle($sm, $para);
						//$rules = RuleTables::getRulesForPara($sm, $para);
//						$result[count($result) - 1]['paras'][strval($para)] = array('title' => $title, 'rules' => $rules, 'act_rules' => array());
						$result[count($result) - 1]['paras'][strval($para)] = array('title' => $title, 'act_rules' => array());
					}
					if (array_key_exists(strval($article->id_rule), $result[count($result) - 1]['paras'][strval($para)]['act_rules']) == false) {
						$result[count($result) - 1]['paras'][strval($para)]['act_rules'][strval($article->id_rule)] = RuleTables::getRuleNum($sm, $article->id_rule);
					}
				}
			}
	        if ($article->id_ortho != null && array_key_exists(strval($article->id_ortho), $result[count($result) - 1]['orthos']) == false) {
				$ortho = OrthogrTables::getOrthogr($sm, $article->id_ortho);
//				$formulas = FormulaTables::getFormulas($sm, $article->id_ortho);
//	            $result[count($result) - 1]['orthos'][strval($article->id_ortho)] = array('name' => $ortho, 'formulas' => $formulas);
	            $result[count($result) - 1]['orthos'][strval($article->id_ortho)] = array('name' => $ortho);
	        } 
        }
        //print_r($result);
       // fwrite($fp, "}");
	//	fclose($fp);

        
        return $result;
     }
     
     public static function putArticlesToRtf($sm, $id_array)
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
		if ($fp) {
			fwrite($fp, "{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\froman\\fprq2\\fcharset204{\\*\\fname Times New Roman;}Times New Roman CYR;}{\\f1\\froman\\fprq2\\fcharset204 Times Roman Cyr Acsent;}}");
	        foreach ($articles as $article)
	        {
				fwrite($fp, "\\par".$article->rtf);
	        }
	        //print_r($result);
	        fwrite($fp, "}");
			fclose($fp);
			return $filename;
		}
		return false;
     }
 } 
 
 ?>
 
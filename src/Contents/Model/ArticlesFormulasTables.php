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

 class ArticlesFormulasTables
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

    public static function getArtCountForFormula($sm, $id_formula)
    {
        $table = $sm->get('Contents\Model\ArticlesFormulasTables');
        $articles = $table->tableGateway->select(function(Select $select) use ($id_formula)
        {
            $select->where('af.id_formula = '.strval($id_formula));
        });
        if ($articles->count())
            return ($articles->count());
        else
        	return 0; 
    }

    public static function getArticlesForFormula($sm, $id_formula, $paginated = false, $count_for_page = 0, $page = 0)
    {
        $table = $sm->get('Contents\Model\ArticlesFormulasTables');
        $articles = $table->tableGateway->select(function(Select $select) use ($id_formula)
        {
            $select->where('af.id_formula = '.strval($id_formula));
            $select->order('af.id');
        });
        if (count($articles) == 0)
        	return array();
        	
        $id_array = array();
        foreach ($articles as $article) {
        	if (isset($article->id)) {
				$id_array[] = array('id' => $article->id, 'marks' => array());
			}
//        		else {
//        			$id_array[count($id_array) - 1]['marks'][] =  array('start' => $article->start, 'len' => $article->len);
//        		}
//        	$id_array[] = $article->id;
        }
        
		if ($paginated) {
			$paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($id_array));
			$paginator->setItemCountPerPage($count_for_page);
			$paginator->setCurrentPageNumber((int)$page);
	        
			$result = array('paginator' => $paginator, 'show' => array(), 'count' => count($id_array));
			if (count($id_array)) {
				$show = [];
				foreach ($paginator->getCurrentItems() as $item) {
					$show[] = $item;
					//error_log($item);
				}
				$result['show'] = ArticleTables::getArticles($sm, $show);//$id_array);
	        }
		}
		else {
//			$result = $id_array; //ArticleTables::getArticles($sm, $id_array);
			foreach ($id_array as $item) {
				$result[] = $item['id']; //$id_array; //ArticleTables::getArticles($sm, $id_array);
			}
		}
//		error_log(sprintf("HistoricTables::getArticles %d", count($result)));		
        return $result;
        
//        $result = ArticleTables::getArticles($sm, $id_array);
//        return $result;
    }

    public static function getFormulasForArticle($sm, $id_art, $id_ortho)
    {
        $table = $sm->get('Contents\Model\ArticlesFormulasTables');
        $articles = $table->tableGateway->select(function(Select $select) use ($id_art, $id_ortho)
        {
            $select->join(array('f' => 'formulas'), 'f.id = af.id_formula', array('id_formula' => 'id'), 'left'); 
            $select->where('af.id = '.strval($id_art).' AND f.id_ortho='.strval($id_ortho));
        });
        if (count($articles) == 0)
        	return array();
        	
        $id_array = array();
        foreach ($articles as $article) {
        	$id_array[] = $article->id_formula;
        }
		return $id_array;        
    }
    
 } 
 
 ?>
 
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

 class HistoricTables
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

	public static function getAllHistoric($sm)
	{
		$table = $sm->get('Contents\Model\HistoricTables');
        $resultSet = $table->tableGateway->select();
        $hist = array();
		foreach ($resultSet as $item) {
			$hist[$item->id] = $item->name." (".$item->abbr.")";
		}
		return $hist;
	}
	
	public static function getName($sm, $id) 
	{
		$table = $sm->get('Contents\Model\HistoricTables');
        $resultSet = $table->tableGateway->select(function(Select $select) use ($id)
        {
            $select->where('h.id = '.strval($id));
        });
        $hist = "";
		if ($resultSet->count())
            $hist = $resultSet->current()->name." (".$resultSet->current()->abbr.")"; 
		return $hist;
	}
	
     public static function getArticles($sm, $id, $paginated = false, $count_for_page = 0, $page = 0)
     {
        error_log("HistoricTables: getArticles");
        error_log($id);
        $table = $sm->get('Contents\Model\HistoricTables');
        $articles = $table->tableGateway->select(function(Select $select) use ($id)
        {
        	$select->join(array('ah' => 'articles_historic'), 'ah.id_historic = h.id', array('id_article' => 'id'), 'left'); 
            $select->where('h.id = '.strval($id));
        });
		        
        $id_array = array();
        foreach ($articles as $article) {
        	if (isset($article->id_article))
				$id_array[] = array('id' => $article->id_article, 'marks' => array());
        	
//	        	$id_array[] = $article->id_article;
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
			foreach ($id_array as $item) {
				$result[] = $item['id']; //$id_array; //ArticleTables::getArticles($sm, $id_array);
			}
//			$result = $id_array; //ArticleTables::getArticles($sm, $id_array);
		}
//		error_log(sprintf("HistoricTables::getArticles %d", count($result)));		
        return $result;
     
     }     
	
 } 
 
 ?>
 
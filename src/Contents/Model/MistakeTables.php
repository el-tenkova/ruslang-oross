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

 class MistakeTables
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

	public static function checkQuery($word)
	{
		if (strpos($word, "*") !== false || strpos($word, "?") !== false)
			return false;
		$word = str_replace("'", "", $word);			
		$word = preg_replace("/[\[|\]|\{|\}|\d|\$|\%|\^|\&|\(|\)|\!|\:|\;|\#|\-|\.|\s]/", "", $word);
       	return $word;
	}

	public static function soundEx($word)
	{
		$word = preg_replace("/[а|А|И|и|Э|э|Ю|ю|У|у|Ы|ы|Е|е|Ё|ё|Я|я|О|о]/u", "", $word);
       	return $word;
	}

    public static function getRelArticles($sm, $query, $where = 3, $yo = 0, $paginated = false, $count_for_page = 0, $page = 0)
	{
		error_log("MistakeTables: getRelArticles");
		error_log(sprintf("mistake = %s", $query));
		
		for ($i = 0; $i < 2; $i++) {
			if ($i == 0)
				$word = MistakeTables::checkQuery($query);
/*			else {
				$word = MistakeTables::soundEx($word);
			} */
			if ($word === false)
				return false;
				
			$arts = WordTables::getArticles($sm, $word, $where, $yo, $paginated, $count_for_page, $page);
			if (count($arts) > 0) {
				return $arts;
			}
				
	        $table = $sm->get('Contents\Model\MistakeTables');
			$words = $table->tableGateway->select(function(Select $select) use ($word)
			{
				$select->join(array('wm' => 'words_mistakes'), 'm.id = wm.id_mistake', array('id_word' => 'id'), 'left');
		        $select->join(array('w' => 'words'), 'w.id = wm.id', array('word'), 'left');
		        $select->where('m.mistake LIKE \''.$word.'\'');
		    }); 
		    if (count($words) > 0) {
		    	break;
		    }
		}
	   // print_r($words);
	    if (count($words) == 1) {
			return WordTables::getArticles($sm, $words->current()->word, $where, $yo, $paginated, $count_for_page, $page);	
			    	
	    }
	    elseif (count($words) > 1) {
	    	error_log(count($words));
	    	$articles = array();
	    	foreach ($words as $word) {
				$articles = array_merge($articles, WordTables::getArticles($sm, $word->word, 1, $yo, $paginated, $count_for_page, $page));
		    	//print_r($articles);
	    	}
	    	return $articles;
//	    	print_r($articles);

/*			$paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($articles));
			$paginator->setItemCountPerPage($count_for_page);
			$paginator->setCurrentPageNumber((int)$page);
	        
			$arts = array('paginator' => $paginator, 'show' => array(), 'count' => count($articles));
			if (count($arts)) {
				$show = [];
				foreach ($paginator->getCurrentItems() as $item) {
					$show[] = $item;
				}
				$arts['show'] = ArticleTables::getArticles($sm, $show);
	        } */
		}
		return $arts;
     }
 } 
 
 ?>
 
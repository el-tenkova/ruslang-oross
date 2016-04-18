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
		$word = preg_replace("/[\[|\]|\{|\}|\d|\$|\%|\^|\&|\(|\)|\!|\:|\;|\#|\-|\.|\s]/", "", $word);
       	return $word;
	}

    public static function getRelArticles($sm, $query, $where = 3, $paginated = false, $count_for_page = 0, $page = 0)
	{
		error_log("MistakeTables: getRelArticles");
		error_log(sprintf("mistake = %s", $query));
		
		$word = MistakeTables::checkQuery($query);
		if ($word === false)
			return false;
			
		$arts = WordTables::getArticles($sm, $word, $where, $paginated, $count_for_page, $page);
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
	   // print_r($words);
	    if (count($words) > 0) {
			$arts = WordTables::getArticles($sm, $words->current()->word, $where, $paginated, $count_for_page, $page);
			return $arts;
	    }
     }
 } 
 
 ?>
 
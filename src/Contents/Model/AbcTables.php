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
 
 class AbcTables
 {
    protected $tableGateway;
    static $abc = [
			'А' => 'a',
			'Б' => 'be',
			'В' => 've',
			'Г' => 'ghe',
			'Д' => 'de',
			'Е(Ё)' => 'e',
			'Ж' => 'zhe',
			'З' => 'ze',
			'И' => 'i',
			'Й' => 'short_i',
			'К' => 'ka',
			'Л' => 'el',
			'М' => 'em',
			'Н' => 'en',
			'О' => 'o',
			'П' => 'pe',
			'Р' => 'er',
			'С' => 'es',
			'Т' => 'te',
			'У' => 'u',
			'Ф' => 'ef',
			'Х' => 'ha',
			'Ц' => 'tse',
			'Ч' => 'che',
			'Ш' => 'sha',
			'Щ' => 'shcha',
			'Ы' => 'ery',
			'Э' => 'reverse_e',
			'Ю' => 'yu',
			'Я' => 'ya',
        ];
	
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public static function getLetter($sm, $letter)
    {
        $table = $sm->get('Contents\Model\AbcTables_'.$letter);
        $parts = $table->tableGateway->select(function(Select $select)
        {
            $select->where('1');
        });
        $ret = array();
        foreach ($parts as $part) {
            $ret[] = array('id_first' => $part->id_first,
            			   'id_last' => $part->id_last,
                           'a_first' => $part->a_first,
            			   'a_last' => $part->a_last); 
        }
        return $ret;
    	
    }
    
    public static function getPrev($sm, $letter, $start)
    {
        $table = $sm->get('Contents\Model\AbcTables_'.$letter);
        $id_last = $start;
        $parts = $table->tableGateway->select(function(Select $select) use($id_last)
        {
            $select->where('id_last < '.strval($id_last));
        });
        $ret = array();
        if (count($parts) > 0) {
        	error_log(count($parts));
        	$idx = 0;
        	foreach ($parts as $part) {
        		if ($idx == count($parts) - 1) {
		        	$ret[] = $part->a_last;
        			$ret[] = $part->id_first;
        			$ret[] = $part->id_last;
        		}
        		$idx++;
        	}
        }
        return $ret;
    	
    }
    
    public static function getNext($sm, $letter, $end)
    {
        $table = $sm->get('Contents\Model\AbcTables_'.$letter);
        $id_first = $end;
        $parts = $table->tableGateway->select(function(Select $select) use($id_first)
        {
            $select->where('id_first > '.strval($id_first));
        });
        $ret = array();
        if (count($parts) > 0) {
        	$ret[] = $parts->current()->a_first;
        	$ret[] = $parts->current()->id_first;
        	$ret[] = $parts->current()->id_last;
        }
        return $ret;
    	
    }
    
    public static function getABC()
    {
    	return @self::$abc;  
    }      
    
    public static function correctAbc($sm, $id)
    {
    	foreach (@self::$abc as $key => $letter) {
	        $table = $sm->get('Contents\Model\AbcTables_'.$letter);
	        for ($i = 0; $i != 2; $i++) {
	        	$where = strval($id);
	        	if ($i == 0)
	        		$where = 'id_first = '.$where;
	        	else
	        		$where = 'id_last = '.$where;
	        	$f = $table->tableGateway->select(function(Select $select) use($where)
    	    	{
        	    	$select->where($where);
        		});
        		if (count($f) > 0) {
        			$found = false;
        			$idx = $id;
        			$title = null;
        			do {
        				if ($i == 0)
				        	$idx = $idx + 1;
				        else
				        	$idx = $idx - 1;
			        	$title = ArticleTables::getTitle($sm, $idx);
        				if ($title  != null)
        					$found = true;
        			}
        			while ($found === false);
        		
        			// delete 
					$action = new Delete('abc_'.$letter);
					if ($i == 0)
	        			$action->where(array('id_first = ?' => $id));
	        		else
	        			$action->where(array('id_last = ?' => $id));

         			$sql    = new Sql($sm->get('Zend\Db\Adapter\Adapter'));
         			$stmt   = $sql->prepareStatementForSqlObject($action);
         			$result = $stmt->execute();		
        			// insert
        			if ($i == 0)
						$table->tableGateway->insert(array('id_first' => $idx,
		                	'id_last' => $f->current()->id_last,
	    	            	'a_first' => $title,
	        	        	'a_last' => $f->current()->a_last,
            			));
            		else
						$table->tableGateway->insert(array('id_first' => $f->current()->id_first,
		                	'id_last' => $idx,
	    	            	'a_first' => $f->current()->a_first,
	        	        	'a_last' => $title,
            			));
        			break;
        		}
        	}
    	}
    }
 } 
 
 ?>
 
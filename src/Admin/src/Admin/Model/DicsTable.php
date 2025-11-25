<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Model;

 use Zend\Db\TableGateway\TableGateway;
 use Zend\Db\Sql\Select;
 use Zend\Db\Sql\Update;
 use Zend\Db\Sql\Sql;
// use Zend\Db\Sql\Expression;
 
 class DicsTable
 {
    protected $tableGateway;
    
    const Made = 3;
    const Expects = 2;
    const Unknown = 1;
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
    public static function getAll($sm)
    {
		$table = $sm->get('Admin\Model\DicsTable');
		$dics = $table->tableGateway->select(function(Select $select)
	    {
		});
		if (count($dics) > 0) {
			$res = array();
			foreach ($dics as $item) {
				$status = 'Не определён';
				if ($item->status == DicsTable::Made)
					$status = 'Готово';
				elseif ($item->status == DicsTable::Expects)
					$status = 'Выполняется';
				$res[] = array('id' => $item->id,
				                       'name' => $item->name,
				                       'path' => $item->path,
				                       'status' => $status,
				                       'chd' => $item->chd);
			}
			return $res;
		}
		return null;
    }	
    
    public static function updateState($sm, $id, $state, $chdate)
    {
		$action = new Update('dics');
		$action->set(array('status' => $state, 'chd' => $chdate, 'path'=>''));
        $action->where(array('id = ?' => $id));

         $sql    = new Sql($sm->get('Zend\Db\Adapter\Adapter'));
         $stmt   = $sql->prepareStatementForSqlObject($action);
         $result = $stmt->execute();		
    }	
 } 
 
 ?>
 
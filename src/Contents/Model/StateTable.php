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
 use Zend\Db\Sql\Update;
 use Zend\Db\Sql\Sql;
 use Zend\Db\Sql\Expression;
 
 class StateTable
 {
	const Ok = 1;
	const UnderReconstruction = 2;
    
    const Success=1;
    const Failed=2;
    const InProcess=3;
    
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
    
    public static function getLastTime($last)
    {
            $ar = strptime ($last, '%Y-%m-%d %H:%M:%S');
             return sprintf('%02d-%02d-%d %02d:%02d:%02d', $ar['tm_mday'], $ar['tm_mon'] + 1, $ar['tm_year'] + 1900, $ar['tm_hour'], $ar['tm_min'], $ar['tm_sec']);            
    }
    
    public static function getState($sm)
    {
        $table = $sm->get('Contents\Model\StateTable');
        $state = $table->tableGateway->select(function(Select $select)
        {
        });
        if ($state->count()) {
            return (array('state' => $state->current()->state,
                                 'last' => StateTable::getLastTime($state->current()->last),
                                 'result' => $state->current()->result)); 
        }
        return array();
    }
    
    public static function setInProcessState($sm)
    {
        //error_log(date('d-m-Y H-i-s'));
		$action = new Update('status');
		$action->set(array('state' => StateTable::UnderReconstruction, 'result' => StateTable::InProcess, 'last'=> date('Y-m-d H-i-s')));
		//$action->set(array('last'=> date('Y-m-d H-i-s')));

         $sql    = new Sql($sm->get('Zend\Db\Adapter\Adapter'));
         $stmt   = $sql->prepareStatementForSqlObject($action);
         $result = $stmt->execute();		
    }
    
 } 
 
 ?>
 
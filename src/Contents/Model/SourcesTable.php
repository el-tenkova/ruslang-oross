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
// use Zend\Db\Sql\Update;
// use Zend\Db\Sql\Sql;
// use Zend\Db\Sql\Expression;
 
 class SourcesTable
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
    
    public static function getAll($sm)
    {
        $table = $sm->get('Contents\Model\SourcesTable');
        $sources = $table->tableGateway->select(function(Select $select) 
        {
            $select->order('s.name ASC');
        });
        $result = array();
        foreach ($sources as $item) {
            $result[strval($item->id)] = $item->name;
        }
        return $result;
    }

    public static function getSrcById($sm, $id)
    {
        $table = $sm->get('Contents\Model\SourcesTable');
        $sources = $table->tableGateway->select(function(Select $select) use($id)
        {
            $select->where('s.id='.strval($id));
        });
        return $sources->current()->name;
    }

    public static function add($sm, $text)
    {
        $table = $sm->get('Contents\Model\SourcesTable');
	    $table->tableGateway->insert(array('name' => $text));
    }
 } 
 
 ?>
 
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
 use Zend\Db\Sql\Expression;
 
 class PredislTable
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
    
    public static function getPredisl($sm)
    {
        $table = $sm->get('Contents\Model\PredislTable');
        $predisl = $table->tableGateway->select(function(Select $select)
        {
            $select->where('id=1');
        });
        if ($predisl->count())
            return (array('title' => $predisl->current()->title,
                                 'sub_title' => $predisl->current()->sub_title,
                                 'empl' => $predisl->current()->empl,
            			         'text' => $predisl->current()->text,
            			         'lit_title' => $predisl->current()->lit_title,
            			         'lit' => $predisl->current()->lit)); 
        return $ret;
    	
    }
    public static function getNewPredisl($sm)
    {
        $table = $sm->get('Contents\Model\PredislTable');
        $predisl = $table->tableGateway->select(function(Select $select)
        {
            $select->where('id=2');
        });
        if ($predisl->count())
            return (array('title' => $predisl->current()->title,
                                 'sub_title' => $predisl->current()->sub_title,
                                 'empl' => $predisl->current()->empl,
            			         'text' => $predisl->current()->text,
            			         'lit_title' => $predisl->current()->lit_title,
            			         'lit' => $predisl->current()->lit)); 
        return $ret;
    }
    public static function getLiterature($sm)
    {
        $table = $sm->get('Contents\Model\PredislTable');
        $predisl = $table->tableGateway->select(function(Select $select)
        {
            $select->where('id=2');
        });
        if ($predisl->count())
            return (array('title' => $predisl->current()->title,
                                 'lit_title' => $predisl->current()->lit_title,
            			         'lit' => $predisl->current()->lit)); 
        return $ret;
    }    
 } 
 
 ?>
 
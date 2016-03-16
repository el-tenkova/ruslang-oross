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

 class ParagraphTables
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

     public static function getParaTitle($sm, $id)
     {
        //error_log("getParaTitle");
        $table = $sm->get('Contents\Model\ParagraphTables');
        //error_log("getParaTitle 2");
        $title = $table->tableGateway->select(function(Select $select) use ($id)
        {
            $select->where('p.id = '.strval($id));
        });
        if ($title->count())
            return ($title->current()->title); 
        return false;        
     }
     
	public static function getAllParaWithOrtho($sm)
	{
        $table = $sm->get('Contents\Model\ParagraphTables');
        $paras = $table->tableGateway->select(function(Select $select)
        {
            $select->join(array('o' => 'orthos'), 'o.id_para = p.id', array('id_ortho' => 'id', 'orthoname' => 'name'), 'left');
            $select->join(array('f' => 'formulas'), 'f.id_ortho = o.id', array('id_formula' => 'id', 'formname' => 'name'), 'left');
            $select->order('p.id');
        });
        $result = array();
        $paraId = 0;
        $orthOd = 0;
        foreach ($paras as $para)
        {
        	if ($paraId != $para->id) {
        		$result[] = array('id_para' => $para->id, 'title' => $para->title, 'ortho' => array('formulas' => array()));
        	}
        }
        return $result;
	}
 } 
 
 ?>
 
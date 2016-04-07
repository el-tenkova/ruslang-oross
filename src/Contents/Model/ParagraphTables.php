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

     public static function getParaFullTitle($sm, $id)
     {
        //error_log("getParaTitle");
        $table = $sm->get('Contents\Model\ParagraphTables');
        //error_log("getParaTitle 2");
        $title = $table->tableGateway->select(function(Select $select) use ($id)
        {
            $select->join(array('tp' => 'tiles_paras'), 'tp.id_para = p.id', array('id_tile'), 'left');
            $select->join(array('t' => 'tiles'), 'tp.id_tile = t.id', array('id' => 'id', 'tile_name' => 'name'), 'left');
            $select->join(array('pt' => 'parts_tiles'), 'pt.id_tile = t.id', array('id_part'), 'left');
            $select->join(array('parts' => 'parts'), 'parts.id = pt.id_part', array('id' => 'id', 'part_name' => 'name'), 'left');
            $select->where('p.id = '.strval($id));
        });
        if ($title->count()) {
            $res = array('title' => $title->current()->title, 'tile' => $title->current()->tile_name, 'part' => $title->current()->part_name); 
            //print_r($res);
            return $res;
        }
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

	public static function getParentTile($sm, $id_para)
	{
        $table = $sm->get('Contents\Model\ParagraphTables');
        $para = $table->tableGateway->select(function(Select $select) use ($id_para)
        {
            $select->join(array('tp' => 'tiles_paras'), 'tp.id_para = p.id', array('id_tile'), 'left');
            $select->where('p.id = '.strval($id_para));
        });
        if ($para->count())
            return ($para->current()->id_tile); 
        return false;        
	}
	
 } 
 
 ?>
 
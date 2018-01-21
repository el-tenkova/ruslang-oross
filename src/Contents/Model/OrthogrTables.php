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

 class OrthogrTables
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

     public static function getAllOrthogr($sm)
     {
        //error_log("getOrthogr");
        $table = $sm->get('Contents\Model\OrthogrTables');
        $orthogr = $table->tableGateway->select(function(Select $select)
        {
            $select->join(array('f' => 'formulas'), 'f.id_ortho = o.id', array('formname' => 'name', 'id_form' => 'id'), 'left');
            $select->order('o.id_para, f.id');            
        });
        $result = array();
        if ($orthogr->count()) {
            $id = 0;
            foreach ($orthogr as $ortho)
            {
                if ($ortho->id != $id) {
                    $id = $ortho->id;
                    $result[] = array('id' => $ortho->id, 'name' => $ortho->name, 'para' => $ortho->id_para, 'formulas' => array());
                }
                $result[count($result) - 1]['formulas'][] = array('id' => $ortho->id_form, 'name' => $ortho->formname);
            }
        }
        return $result;        
     }

     public static function getOrthogr($sm, $id)
     {
        //error_log("getOrthogr");
        $table = $sm->get('Contents\Model\OrthogrTables');
        $orthogr = $table->tableGateway->select(function(Select $select) use ($id)
        {
            $select->where('o.id = '.strval($id));
        });
        if ($orthogr->count())
            return ($orthogr->current()->name); 
        return false;        
     }
     
     public static function getFRTF($sm, $id_ortho)
     {
        $table = $sm->get('Contents\Model\OrthogrTables');
        $formulas = $table->tableGateway->select(function(Select $select) use ($id_ortho)
        {
            $select->where('o.id = '.strval($id_ortho));
        });
        return $formulas->current()->rtf;
     }
     
     public static function getFormulasForPara($sm, $id_para)
     {
        //error_log("getFormulas");
        $table = $sm->get('Contents\Model\OrthogrTables');
        $orthos = $table->tableGateway->select(function(Select $select) use ($id_para)
        {
            $select->join(array('f' => 'formulas'), 'f.id_ortho = o.id', array('formname' => 'name', 'example' => 'example', 'rest' => 'rest'), 'left');
            $select->where('o.id_para = '.strval($id_para));
            $select->order('f.id');
        });
        $result = array();
        foreach ($orthos as $ortho)
        {
            $result[] = array('ortho' => $ortho->name, 'formula' => $ortho->formname, 'example' => $ortho->example, 'rest' => $ortho->rest);
        }
        return $result;
     }
     
     public static function getFormulasForRule($sm, $id_rule)
     {
       // error_log("getFormulasForRule");
        $table = $sm->get('Contents\Model\OrthogrTables');
        $orthos = $table->tableGateway->select(function(Select $select) use ($id_rule)
        {
            $select->join(array('f' => 'formulas'), 'f.id_ortho = o.id', array('formname' => 'name', 'id_form' => 'id', 'example' => 'example', 'rest' => 'rest', 'is_prefix' => 'is_prefix'), 'left');
            $select->where('f.id_rule = '.strval($id_rule));
            $select->order('f.id');
        });
        $result = array('words' => array(), 'prefix' => array());
        foreach ($orthos as $ortho)
        {
        	if ($ortho->is_prefix == 0) {
	            if ($ortho->active != 0)
	                $result['words'][] = array('id_ortho' => strval($ortho->id), 'ortho' => $ortho->name, 'active' => $ortho->active, 'formula' => $ortho->formname, 'example' => $ortho->example, 'rest' => $ortho->rest, 'id_form' => strval($ortho->id_form));
	            else
	                $result['words'][] = array('ortho' => $ortho->name, 'active' => strval($ortho->active), 'formula' => $ortho->formname, 'example' => $ortho->example, 'rest' => $ortho->rest, 'id_form' => strval($ortho->id_form));
        	}
        	else {
	            if ($ortho->active != 0)
	                $result['prefix'][] = array('id_ortho' => strval($ortho->id), 'ortho' => $ortho->name, 'active' => $ortho->active, 'formula' => $ortho->formname, 'example' => $ortho->example, 'rest' => $ortho->rest, 'id_form' => strval($ortho->id_form));
	            else
	                $result['prefix'][] = array('ortho' => $ortho->name, 'active' => strval($ortho->active), 'formula' => $ortho->formname, 'example' => $ortho->example, 'rest' => $ortho->rest, 'id_form' => strval($ortho->id_form));
        	}
        }
        return $result;
     }
     
     public static function getFormulasForNull($sm, $id_para)
     {
        //error_log("getFormulasForPara");
        $table = $sm->get('Contents\Model\OrthogrTables');
        $orthos = $table->tableGateway->select(function(Select $select) use ($id_para)
        {
            $select->join(array('f' => 'formulas'), 'f.id_ortho = o.id', array('formname' => 'name', 'id_form' => 'id', 'example' => 'example', 'rest' => 'rest'), 'left');
            $select->where('f.id_para = '.strval($id_para).' AND f.id_rule = 0');
            $select->order('f.id');
        });
        $result = array();
        foreach ($orthos as $ortho)
        {
            if ($ortho->active != 0)
                $result[] = array('id_ortho' => strval($ortho->id), 'ortho' => $ortho->name, 'active' => $ortho->active, 'formula' => $ortho->formname, 'example' => $ortho->example, 'rest' => $ortho->rest, 'id_form' => strval($ortho->id_form));
            else
                $result[] = array('ortho' => $ortho->name, 'active' => strval($ortho->active), 'formula' => $ortho->formname, 'example' => $ortho->example, 'rest' => $ortho->rest, 'id_form' => strval($ortho->id_form));
        }
        return $result;
     }
          
 } 
 
 ?>
 
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

 class FormulaTables
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

     public static function getFormula($sm, $id_formula)
     {
        $table = $sm->get('Contents\Model\FormulaTables');
        $formulas = $table->tableGateway->select(function(Select $select) use ($id_formula)
        {
            $select->where('f.id = '.strval($id_formula));
        });
        return $formulas->current()->name;
     }

     public static function getFRTF($sm, $id_formula)
     {
        $table = $sm->get('Contents\Model\FormulaTables');
        $formulas = $table->tableGateway->select(function(Select $select) use ($id_formula)
        {
            $select->where('f.id = '.strval($id_formula));
        });
        return $formulas->current()->rtf;
     }

     public static function getFormulas($sm, $id_ortho)
     {
        error_log("getFormulas from FormulaTable");
        $table = $sm->get('Contents\Model\FormulaTables');
        $formulas = $table->tableGateway->select(function(Select $select) use ($id_ortho)
        {
            $select->where('f.id_ortho = '.strval($id_ortho));
        });
        $result = array();
        foreach ($formulas as $formula)
        {
            $result[] = array('id' => strval($formula->id), 'name' => $formula->name, 'example' => $formula->example, 'rest' => $formula->rest);
        }
        error_log(count($result));
        return $result;
     }
     
	public static function getAllFormulasWithInfo($sm)
	{
        $table = $sm->get('Contents\Model\FormulaTables');
        $formulas = $table->tableGateway->select(function(Select $select)
        {
            $select->join(array('p' => 'paras'), 'f.id_para = p.id', array('id_para' => 'id', 'para_title' => 'name'), 'left');
            $select->join(array('o' => 'orthos'), 'f.id_ortho = o.id', array('id_ortho' => 'id', 'ortho_name' => 'name', 'ortho_art_count' => 'art_count'), 'left');
            $select->order('p.id, o.id');
        });
        $result = array();
        $paraId = 0;
        $orthOd = 0;
        foreach ($formulas as $formula)
        {
        	if ($paraId != $formula->id_para) {
        		$result[] = array('id_para' => $formula->id_para, 'title' => $formula->para_title, 
        						  'ortho' => array(array('id_ortho' => $formula->id_ortho, 'ortho_name' => $formula->ortho_name, 'art_count' => $formula->ortho_art_count,
        						  'formulas' => array(array('id_formula' => $formula->id, 'name' => $formula->name, 'example' => $formula->example, 'arts_count' => $formula->art_count)))));
        		$paraId = $formula->id_para;
        		$orthoId = $formula->id_ortho;
        	}
        	else {
        		if ($orthoId != $formula->id_ortho) {
        			$result[count($result) - 1]['ortho'][] = array('id_ortho' => $formula->id_ortho, 'ortho_name' => $formula->ortho_name, 'art_count' => $formula->ortho_art_count,
        													       'formulas' => array(array('id_formula' => $formula->id, 'name' => $formula->name, 'example' => $formula->example, 'arts_count' => $formula->art_count)));
	        		$orthoId = $formula->id_ortho;
        		}
        		else {
					$result[count($result) - 1]['ortho'][count($result[count($result) - 1]['ortho']) - 1]['formulas'][] = array('id_formula' => $formula->id, 'name' => $formula->name, 'example' => $formula->example, 'arts_count' => $formula->art_count);
        		}
        	}
        }
        //print_r($result);
/*		$fp = fopen('orthogr_data.txt', 'w');
//fwrite($fp, "Орфограммы и формулы:\tОнлайн\tСправочник\n");
		foreach ($result as $key => $item) {
			foreach ($item['ortho'] as $ortho) {
				//fwrite($fp, $ortho['ortho_name']."\t\t\n");
				foreach ($ortho['formulas'] as $formula) {
					if (strval($formula['arts_count']) == 0)
						fwrite($fp, $formula['name']."\t".strval($formula['arts_count']."\t\n"));
				}
			}
		}
		fclose($fp); */
        return $result;
		
	}
 } 
 
 ?>
 
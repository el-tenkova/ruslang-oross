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

 class RuleTables
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

     public static function getRule($sm, $id)
     {
        //error_log("getRule");
        $table = $sm->get('Contents\Model\RuleTables');
        $rule = $table->tableGateway->select(function(Select $select) use ($id)
        {
            $select->where('r.id = '.strval($id));
        });
        if ($rule->count()) {
            //error_log($rule->current()->text);
            return ('<b>§ '.$rule->current()->id_para.'</b>.</p><p>'.$rule->current()->text); 
        }
        return false;        
     }

	 public static function removeSup($str) 
	 {
		$pos = strpos($str, "<sup>");
	    if ($pos !== false) {
			$result = "";
			while ($pos !== false) {	        	
				$result .= substr($str, 0, $pos);
				$pos = strpos($str, "</sup>", $pos + 5);
				$str = substr($str, $pos + 6);
				$pos = strpos($str, "<sup>");
			}
			$result .= $str;
			return $result;
	    }
	    return $str;
	 }
	 
     public static function getRuleEx($sm, $id)
     {
        //error_log("getRule");
        $table = $sm->get('Contents\Model\RuleTables');
        $rule = $table->tableGateway->select(function(Select $select) use ($id)
        {
            $select->where('r.id = '.strval($id));
        });
        if ($rule->count()) {
            //error_log($rule->current()->text);
            $text = "";
            if (strlen($rule->current()->text) < 500)
	            $text = '<p>'.$rule->current()->text; 
	        else {
	        	$text = substr($rule->current()->text, 0, 495);
	        	$text = substr($text, 0, strrpos($text, ' '))."...";
	        }
	        return RuleTables::removeSup($text);
        }
        return false;        
     }

     public static function getRuleNum($sm, $id)
     {
        //error_log("getRule");
        $table = $sm->get('Contents\Model\RuleTables');
        $rule = $table->tableGateway->select(function(Select $select) use ($id)
        {
            $select->where('r.id = '.strval($id));
        });
        if ($rule->count()) {
            //error_log($rule->current()->text);
            return (strval($rule->current()->num)); 
        }
        return false;        
     }
     
     public static function getRuleFull($sm, $id)
     {
        //error_log("getRulesForPara");
        $table = $sm->get('Contents\Model\RuleTables');
        $rule = $table->tableGateway->select(function(Select $select) use ($id)
        {
            $select->where('r.id = '.strval($id));
        });
        $result = array();
//            error_log(sprintf("%s %s", $rule->num, $rule->text));
		$result['id'] 		= strval($rule->current()->id);
		$restul['num'] 		= strval($rule->current()->num);
		$result['text'] 	= $rule->current()->text;
		$result['info'] 	= $rule->current()->info;
		$result['orthos'] 	= OrthogrTables::getFormulasForRule($sm, $id);
		$result['footnotes'] 	= FootNoteTables::getFootForRule($sm, $id);
		if ($rule->current()->id != $rule->current()->id_parent) {
			$parent_text = RuleTables::getRule($sm, $rule->current()->id_parent);
			if ($parent_text != false) {
				$result['parent'] = $parent_text;
			}
        }
//        print_r($result);
        return $result;
     }
     
     public static function getRulesForPara($sm, $id_para)
     {
        error_log("getRulesForPara");
        $table = $sm->get('Contents\Model\RuleTables');
        $rules = $table->tableGateway->select(function(Select $select) use ($id_para)
        {
            $select->where('r.id_para = '.strval($id_para));
        });
        $result = array();
        foreach ($rules as $rule) {
          //  error_log(sprintf("%s %s", $rule->num, $rule->text));
            $result[] = array('id' => strval($rule->id), 'num' => strval($rule->num), 'text' => $rule->text, 'info' => $rule->info, 'orthos' => OrthogrTables::getFormulasForRule($sm, $rule->id), 'footnotes' => FootNoteTables::getFootForRule($sm, $rule->id));
			if ($rule->id != $rule->id_parent) {
				$parent_text = RuleTables::getRule($sm, $rule->id_parent);
				if ($parent_text != false) {
					$result[count($result) - 1]['parent'] = $parent_text;
					//error_log(sprintf("parent = %s", $result[count($result) - 1]['parent']));
				}
			}
        }
        // if exist orthos connected to rule with id 0 add them to last item in result
        $orthos = OrthogrTables::getFormulasForNull($sm, $id_para);
        if (count($orthos) > 0) {
        	foreach ($orthos as $ortho) {
	        	$result[count($result) - 1]['orthos']['words'][] = $ortho;
        	}
        }
        //print_r($result);
        return $result;
     }

	public static function getPara($sm, $id)
	{
        $table = $sm->get('Contents\Model\RuleTables');
        $rule = $table->tableGateway->select(function(Select $select) use ($id)
        {
            $select->where('r.id = '.strval($id));        	
        });
		if ($rule->count() > 0)
			return $rule->current()->id_para;
		return false;
	}     

	public static function getRules($sm, $id_array, $query = null)
	{
        $table = $sm->get('Contents\Model\RuleTables');

		$id_rules = array();
		foreach ($id_array as $item) {
			$id_rules[] = $item['id'];
		}
		$result = array();
		if (count($id_rules) > 0) {
			$ids = implode(",", $id_rules);
			error_log(sprintf("getRules ids = %s", $ids));
	        $rules = $table->tableGateway->select(function(Select $select) use ($ids)
	        {
	            $select->where('r.id IN ('.$ids.')');
	            $select->order('r.id_para, r.id');					
	        });
			if ($rules->count() > 0) {
				$paraId = 0;
				foreach ($rules as $rule) {
					if ($rule->id_para != $paraId) {
						$paraId = $rule->id_para;
						$title = ParagraphTables::getParaTitle($sm, $paraId);
						$title = RuleTables::removeSup(substr($title, strpos($title, ".") + 1));
						$result[] = array('para' => strval($paraId), 'title' => $title, 'rules' => array(array('id' => strval($rule->id), 'name' => RuleTables::getRuleEx($sm, $rule->id))));
					}
					else {
						$result[count($result) - 1]['rules'][] = array('id' => strval($rule->id), 'name' => RuleTables::getRuleEx($sm, $rule->id));
					}				
				}
				if (count($result) == 1) {
					$result[0]['rules'][0]['marks'] = $id_array[0]['marks'];
				}
			}
		}
        return $result;
	}

	public static function getParaContents($sm, $id_para)
	{
        $table = $sm->get('Contents\Model\RuleTables');
		$rules = $table->tableGateway->select(function(Select $select) use ($id_para)
		{
			$select->where('r.id_para='.$id_para.' AND r.title != \'\'');
			$select->order('r.id');					
		});
		$titles = array();
		if (count($rules) > 0) {
			foreach ($rules as $rule) {
				$titles[] = array($rule->num, $rule->title);
			}
		}
		return $titles;
		//print_r($titles);	
	}
	     
} 
 
?>
 
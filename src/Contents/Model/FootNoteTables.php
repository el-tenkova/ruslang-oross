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

 class FootNoteTables
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

	public static function getFootForPara($sm, $id_para)
	{
        error_log("getFootForPara");
        $tile = ParagraphTables::getParentTile($sm, $id_para);
        error_log($tile);
        $table = $sm->get('Contents\Model\FootNoteTables');
        $foots = $table->tableGateway->select(function(Select $select) use ($id_para, $tile)
        {
            //$select->join(array('fp' => 'footnotes_paras'), 'fp.id = f.id', array(), 'left');
            $select->where('f.id_para = '.strval($id_para).' OR (f.id_para=0 AND f.id_tile='.strval($tile).')');
            $select->order('f.id');
        });
        $result = array();
        foreach ($foots as $foot)
        {
            $result[] = array('footnote' => $foot->text);
        }
        return $result;
		
	}

	public static function getFootForRule($sm, $id_rule)
	{
        error_log("getFootNotes");
        $table = $sm->get('Contents\Model\FootNoteTables');
        $foots = $table->tableGateway->select(function(Select $select) use ($id_rule)
        {
          //  $select->join(array('fr' => 'footnotes_rules'), 'fr.id = f.id', array(), 'left');
            $select->where('f.id_rule = '.strval($id_rule));
            $select->order('f.id');
        });
        $result = array();
        foreach ($foots as $foot)
        {
            $result[] = array('id' => strval($foot->id), 'footnote' => $foot->text);
        }
        return $result;
		
	}
 } 
 
 ?>
 
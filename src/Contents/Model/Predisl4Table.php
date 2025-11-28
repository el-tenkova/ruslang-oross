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
 use Zend\Db\Sql\Delete;
 use Zend\Db\Sql\Sql;
 use Zend\Db\Sql\Expression;
 
 define ("Main", 1);
 define ("Bibliogr", 2);
 
 class Predisl4Table
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
    
    public static function getMain($sm)
    {
        $table = $sm->get('Contents\Model\Predisl4Table');
        $predisl4 = $table->tableGateway->select(function(Select $select)
        {
            $select->where('id='.Main);
        });
        if ($predisl4->count())
            return (array('title' => $predisl4->current()->title,
            			  'text' => $predisl4->current()->text)); 
        return false;
    	
    }
    public static function getBibliogr($sm)
    {
        $table = $sm->get('Contents\Model\Predisl4Table');
        $bibliogr = $table->tableGateway->select(function(Select $select)
        {
            $select->where('id='.Bibliogr);
        });
        if ($bibliogr->count())
            return (array('title' => $bibliogr->current()->title,
            			  'text' => $bibliogr->current()->text)); 
        return false;
    	
    }

    public static function getPagesDescr($sm)
    {
        $table = $sm->get('Contents\Model\Predisl4Table');
        $result = $table->tableGateway->select(function(Select $select)
        {
            $select->where(1);
        });
        
        if ($result->count())
        {
            $pages = array();
			foreach ($result as $page) {
				$pages[] = array('page_title' => $page->page_title,
				                            'id' => $page->id);
            }
            return $pages;
        }
        return false;
    }
    public static function getPage($sm, $id)
    {
        $table = $sm->get('Contents\Model\Predisl4Table');
        $page = $table->tableGateway->select(function(Select $select) use ($id)
        {
            $select->where('id='.$id);
        });
        if ($page->count())
            return (array('text' => $page->current()->text)); 
        return false;    	
    }
	public static function savePageEdited($sm, $id, $text)
	{
	    error_log("savePageEdited");
        $sql    = new Sql($sm->get('Zend\Db\Adapter\Adapter'));
		$action = $sql->update();
		$action->table('predisl4');
		$action->set(array('text' => $text));
        $action->where(array('id' => $id));
        $stmt   = $sql->prepareStatementForSqlObject($action);
        $res = $stmt->execute();		
        return $res;
	}
 } 
 
 ?>
 
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

 class ArticleAddInfoTable
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
        $table = $sm->get('Contents\Model\ArticleAddInfoTable');
        $infos = $table->tableGateway->select(function(Select $select)
        {
            $select->order('ai.id_article');
        });
        $result = array();
        foreach ($infos as $info)
        {
        	$result[] = array('id' => $info->id, 'id_article' => $info->id_article, 'key_article' => $info->key_article);
        }
        return $result;
    }
    public static function getAllForArticle($sm, $key_article)
    {
        $table = $sm->get('Contents\Model\ArticleAddInfoTable');
        $infos = $table->tableGateway->select(function(Select $select) use ($key_article)
        {
            $select->where('ai.key_article = '.strval($key_article));
        });
        $result = array();
        foreach ($infos as $info)
        {
        	$result[] = array('id' => $info->id, 'id_src' => $info->id_src, 'text' => $info->text);
        }
        return $result;
    }
    public static function add($sm, $key_article, $id_article, $text, $id_src)
    {
    	error_log("add info");
        $table = $sm->get('Contents\Model\ArticleAddInfoTable');
	    $result = $table->tableGateway->insert(array('text' => $text, 'id_article' => $id_article,'key_article' => $key_article, 'id_src' => $id_src));
	    return $table->tableGateway->lastInsertValue;
    }
    public static function update($sm, $id, $text, $id_src)
    {
		$action = new Update('articles_addinfo');
		$action->set(array('text' => $text, 'id_src' => $id_src));
        $action->where(array('id = ?' => $id));

         $sql    = new Sql($sm->get('Zend\Db\Adapter\Adapter'));
         $stmt   = $sql->prepareStatementForSqlObject($action);
         $result = $stmt->execute();		
	    return 1;
    }
    public static function del($sm, $id)
    {
    	error_log("delete info");
		$action = new Delete('articles_addinfo');
        $action->where(array('id = ?' => $id));

         $sql    = new Sql($sm->get('Zend\Db\Adapter\Adapter'));
         $stmt   = $sql->prepareStatementForSqlObject($action);
         $result = $stmt->execute();		
    }
    public static function getTextById($sm, $id)
    {
        $table = $sm->get('Contents\Model\ArticleAddInfoTable');
        $sources = $table->tableGateway->select(function(Select $select) use($id)
        {
            $select->where('ai.id='.strval($id));
        });
        return $sources->current()->text;
    }
    
 } 
 
 ?>
 
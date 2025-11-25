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

 class ArticleLinkTable
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
        $table = $sm->get('Contents\Model\ArticleLinkTable');
        $infos = $table->tableGateway->select(function(Select $select)
        {
            $select->order('al.id');
        });
        $result = array();
        foreach ($infos as $info)
        {
        	$result[] = array('id' => $info->id);
        }
        return $result;
    }
    public static function getLinkById($sm, $id)
    {
        $table = $sm->get('Contents\Model\ArticleAddLinkTable');
        $sources = $table->tableGateway->select(function(Select $select) use($id)
        {
            $select->where('al.id='.strval($id));
        });
        return $sources->current()->link;
    }
    public static function getIdByLink($sm, $link)
    {
        $table = $sm->get('Contents\Model\ArticleLinkTable');
        $sources = $table->tableGateway->select(function(Select $select) use($link)
        {
            $select->where('al.link=\''.$link.'\'');
        });
        return $sources->current()->id;
    }
    
 } 
 
 ?>
 
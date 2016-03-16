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

 class TilesTable
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

     public function getTile($id)
     {
         $id  = (int) $id;
         $rowset = $this->tableGateway->select(array('id' => $id));
         $row = $rowset->current();
         if (!$row) {
             throw new \Exception("Could not find row $id");
         }
         return $row;
     }

     public function getTilesForPart($id_part)
     {
         $id  = (int) $id;
         $sqlSelect = $this->tableGateway->getSql()->select();
         $sqlSelect->columns(array('name'));
         $sqlSelect->join('parts_tiles', 'othertable.id = yourtable.id', array(), 'left');

$resultSet = $this->tableGateway->selectWith($sqlSelect);
return $resultSet;         
         $rowset = $this->tableGateway->select(array('id' => $id))->join(;
         $row = $rowset->current();
         if (!$row) {
             throw new \Exception("Could not find row $id");
         }
         return $row;
     }

     public function saveTile(Tile $tile)
     {
         $data = array(
             'name' => $tile->name,
         );

         $id = (int) $tile->id;
         if ($id == 0) {
             $this->tableGateway->insert($data);
         } else {
             if ($this->getTile($id)) {
                 $this->tableGateway->update($data, array('id' => $id));
             } else {
                 throw new \Exception('Tile id does not exist');
             }
         }
     }

     public function deleteTile($id)
     {
         $this->tableGateway->delete(array('id' => (int) $id));
     }
 } 
 
 ?>
 
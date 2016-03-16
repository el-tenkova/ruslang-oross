<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Contents\Controller;

 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use Zend\Db\Sql\Sql;
 use Zend\Db\Adapter\Adapter;

 class ContentsController extends AbstractActionController
 {
     protected $contentsTables;
     
     public function indexAction()
     {
        
 /*       $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
  
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('parts', 'name');
//        $select->where(array('id' => 2));
 
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();       
        
        print_r($results);   */
        error_log("ContentsController:indexAction"); 
         return new ViewModel(array(
//             'leftside' => $this->getContentsTables()->getContents(),
             'content' => $this->getContentsTables()->getContents(),
         ));
     }

/*     public function addAction()
     {
     }

     public function editAction()
     {
     }

     public function deleteAction()
     {
     } */
     
     public function getContentsTables()
     {
         //error_log("contentsTable123");
         if (!$this->contentsTables) {
             $sm = $this->getServiceLocator();
             $this->contentsTables = $sm->get('Contents\Model\ContentsTables');
         }
         return $this->contentsTables;
     }     
     
 }
?>
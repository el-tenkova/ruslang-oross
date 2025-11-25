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

 class ParagraphController extends AbstractActionController
 {
     protected $paragraphsTables;
     
   /*  public function indexAction()
     {
        
 /*       $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
  
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('parts', 'name');
//        $select->where(array('id' => 2));
 
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();       
        
        print_r($results);   
         return new ViewModel(array(
             'contents' => $this->getContentsTables()->getContents(),
         ));
     } */

     public function viewAction()
     {
        error_log("Paragraph viewAction");
        return new ViewModel(array(
             'para' => $this->getParagraphsTables()->fetchAll(),
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
     
     public function getParagraphsTables()
     {
         //error_log("contentsTable123");
         if (!$this->paragraphsTables) {
             $sm = $this->getServiceLocator();
             $this->paragraphsTables = $sm->get('Contents\Model\ParagraphsTables');
         }
         return $this->paragraphsTables;
     }     
     
 }
?>
<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Contents\Model;

 class Rule
 {
     public $id;
     public $id_para;
     public $id_parent;
     public $id_foot;
     public $num;
     public $text;
     public $info; 
     public $footnote;

     public function exchangeArray($data)
     {
//        error_log("Paragraph exchangeArray");
        
        $this->id       	= (!empty($data['id'])) ? $data['id'] : null;
        $this->id_para  	= (!empty($data['id_para'])) ? $data['id_para'] : null;
        $this->id_parent 	= (!empty($data['id_parent'])) ? $data['id_parent'] : null;
        $this->id_foot  	= (!empty($data['id_foot'])) ? $data['id_foot'] : null;
        $this->num     	 	= (!empty($data['num'])) ? $data['num'] : null;
        $this->text     	= (!empty($data['text'])) ? $data['text'] : null;
        $this->info     	= (!empty($data['info'])) ? $data['info'] : null;
        $this->footnote    	= (!empty($data['footnote'])) ? $data['footnote'] : null;
     }
 }
 ?>
 
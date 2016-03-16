<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Contents\Model;

 class Paragraph
 {
     public $id;
     public $name;
     public $title;
     public $examples;

     public function exchangeArray($data)
     {
//        error_log("Paragraph exchangeArray");
        
        $this->id     = (!empty($data['id'])) ? $data['id'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->title = (!empty($data['title'])) ? $data['title'] : null;
        $this->examples = (!empty($data['examples'])) ? $data['examples'] : null; 
     }
 }
 ?>
 
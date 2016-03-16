<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Contents\Model;

 class ContentItem
 {
//     public $id_tile;
//     public $id_part;
     public $id;
     public $name;
     public $examples;

     public function exchangeArray($data)
     {
//        error_log("ContentItem exchangeArray");
//        error_log($data['id']);
//        error_log($data['name']);
         $this->id     = (!empty($data['id'])) ? $data['id'] : null;
         $this->name = (!empty($data['name'])) ? $data['name'] : null;
         $this->examples = (!empty($data['examples'])) ? $data['examples'] : null;
 //        $this->id_tile     = (!empty($data['id_tile'])) ? $data['id_tile'] : null;
 //        $this->id_part     = (!empty($data['id_part'])) ? $data['id_part'] : null;
     }
 }
 ?>
 
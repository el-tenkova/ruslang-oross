<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Contents\Model;

 class FootNote
 {
     public $id;
     public $id_para;
     public $id_rule;
     public $text;

     public function exchangeArray($data)
     {
         $this->id   	= (!empty($data['id'])) ? $data['id'] : null;
         $this->id_para = (!empty($data['id_para'])) ? $data['id_para'] : null;
         $this->id_rule = (!empty($data['id_rule'])) ? $data['id_rule'] : null;
         $this->text 	= (!empty($data['text'])) ? $data['text'] : null;
     }
 }
 ?>
 
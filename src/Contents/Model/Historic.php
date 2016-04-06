<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Contents\Model;

 class Historic
 {
     public $id;
     public $name;
     public $abbr;

	 public $id_article;
	 
     public function exchangeArray($data)
     {
         $this->id   = (!empty($data['id'])) ? $data['id'] : null;
         $this->name = (!empty($data['name'])) ? $data['name'] : null;
         $this->abbr = (!empty($data['abbr'])) ? $data['abbr'] : null;

         $this->id_article = (!empty($data['id_article'])) ? $data['id_article'] : null;
     }
 }
 ?>
 
<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Contents\Model;

 class Orthogr
 {
     public $id;
     public $id_para;
     public $name;
     public $active;
     // join
     public $formname;
     public $example;
     public $rest;
     public $id_form;
     public $is_prefix;

     public function exchangeArray($data)
     {
//        error_log("Paragraph exchangeArray");
        
        $this->id           = (!empty($data['id'])) ? $data['id'] : null;
        $this->id_para      = (!empty($data['id_para'])) ? $data['id_para'] : null;
        $this->name         = (!empty($data['name'])) ? $data['name'] : null;
        $this->active       = (!empty($data['active'])) ? $data['active'] : null;
        //
        $this->formname     = (!empty($data['formname'])) ? $data['formname'] : null;
        $this->example      = (!empty($data['example'])) ? $data['example'] : null;
        $this->rest         = (!empty($data['rest'])) ? $data['rest'] : null;
        $this->id_form      = (!empty($data['id_form'])) ? $data['id_form'] : null;
        $this->is_prefix    = (!empty($data['is_prefix'])) ? $data['is_prefix'] : null;
     }
 }
 ?>
 
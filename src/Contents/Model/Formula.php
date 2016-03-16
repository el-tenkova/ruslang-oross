<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Contents\Model;

 class Formula
 {
	public $id;
	public $id_para;
	public $id_rule;
	public $id_ortho;
	public $name;
	public $example;
	public $rest;
	public $is_prefix;
    //
	public $para_title;
	public $ortho_name;
		     

	public function exchangeArray($data)
	{
//        error_log("Paragraph exchangeArray");
        
		$this->id           = (!empty($data['id'])) ? $data['id'] : null;
        $this->id_para      = (!empty($data['id_para'])) ? $data['id_para'] : null;
        $this->id_rule      = (!empty($data['id_rule'])) ? $data['id_rule'] : null;
        $this->id_ortho     = (!empty($data['id_ortho'])) ? $data['id_ortho'] : null;
        $this->name         = (!empty($data['name'])) ? $data['name'] : null;
        $this->example      = (!empty($data['example'])) ? $data['example'] : null;
        $this->rest         = (!empty($data['rest'])) ? $data['rest'] : null;
        $this->is_prefix    = (!empty($data['is_prefix'])) ? $data['is_prefix'] : null;
        // 
        $this->para_title	= (!empty($data['para_title'])) ? $data['para_title'] : null;
        $this->ortho_name	= (!empty($data['ortho_name'])) ? $data['ortho_name'] : null;
     }
 }
 ?>
 
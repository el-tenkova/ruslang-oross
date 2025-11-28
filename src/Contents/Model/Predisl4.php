<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Contents\Model;

 class Predisl4
 {
    public $id;
    public $title; 
    public $page_title;
    public $text;

    public function exchangeArray($data)
    {
        
        $this->id           = (!empty($data['id'])) ? $data['id'] : null;
        $this->title        = (!empty($data['title'])) ? $data['title'] : null;
        $this->page_title        = (!empty($data['page_title'])) ? $data['page_title'] : null;
        $this->text         = (!empty($data['text'])) ? $data['text'] : null;
     }
 }
 ?>
 
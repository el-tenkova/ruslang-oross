<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Contents\Model;

 class Predisl
 {
    public $id;
    public $title;
    public $sub_title;
    public $empl;
    public $text;
    public $lit_title;
    public $lit;

    public function exchangeArray($data)
    {
        
        $this->id           = (!empty($data['id'])) ? $data['id'] : null;
        $this->title        = (!empty($data['title'])) ? $data['title'] : null;
        $this->sub_title   = (!empty($data['sub_title'])) ? $data['sub_title'] : null;
        $this->empl      = (!empty($data['empl'])) ? $data['empl'] : null;
        $this->text         = (!empty($data['text'])) ? $data['text'] : null;
        $this->lit_title    = (!empty($data['lit_title'])) ? $data['lit_title'] : null;
        $this->lit          = (!empty($data['lit'])) ? $data['lit'] : null;
     }
 }
 ?>
 
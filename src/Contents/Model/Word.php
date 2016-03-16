<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Contents\Model;

 class Word
 {
    public $id;
    public $title;
    public $start;
    public $len;
    public $word;
    public $text;
    public $segment;
    public $number;
    public $type;

    public $id_article;
    public $id_para;
    public $id_ortho;
    public $id_rule;
    public $id_formula;
    
    public $id_item;

    public function exchangeArray($data)
    {
//        error_log("Paragraph exchangeArray");
        
        $this->id           = (!empty($data['id'])) ? $data['id'] : null;
        $this->title        = (!empty($data['title'])) ? $data['title'] : null;
        $this->start        = (!empty($data['start'])) ? $data['start'] : null;
        $this->len          = (!empty($data['len'])) ? $data['len'] : null;
        $this->segment      = (!empty($data['segment'])) ? $data['segment'] : null;
        $this->number       = (!empty($data['number'])) ? $data['number'] : null;
        $this->type         = (!empty($data['type'])) ? $data['type'] : null;

        $this->word         = (!empty($data['word'])) ? $data['word'] : null;
        $this->text         = (!empty($data['text'])) ? $data['text'] : null;

        $this->id_article   = (!empty($data['id_article'])) ? $data['id_article'] : null;
        $this->id_para      = (!empty($data['id_para'])) ? $data['id_para'] : null;
        $this->id_ortho     = (!empty($data['id_ortho'])) ? $data['id_ortho'] : null;
        $this->id_rule      = (!empty($data['id_rule'])) ? $data['id_rule'] : null;
        $this->id_formula   = (!empty($data['id_formula'])) ? $data['id_formula'] : null;
 
        $this->id_item      = (!empty($data['id_item'])) ? $data['id_item'] : null;
        
     }
 }
 ?>
 
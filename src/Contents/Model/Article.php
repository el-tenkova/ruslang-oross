<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Contents\Model;

 class Article
 {
    public $id;
    public $id_word;
    public $id_para;
    public $id_ortho;
    public $id_rule;
    public $id_formula;
    public $id_comment;
    
    public $title;
    public $start;
    public $len;
    public $text;
    public $rtf;
    public $src;

    public function exchangeArray($data)
    {
//        error_log("Paragraph exchangeArray");
        
        $this->id           = (!empty($data['id'])) ? $data['id'] : null;
        $this->id_word      = (!empty($data['id_word'])) ? $data['id_word'] : null;
        $this->title         = (!empty($data['title'])) ? $data['title'] : null;
        $this->start         = (!empty($data['start'])) ? $data['start'] : null;
        $this->len         = (!empty($data['len'])) ? $data['len'] : null;
        $this->text         = (!empty($data['text'])) ? $data['text'] : null;
        $this->rtf         = (!empty($data['rtf'])) ? $data['rtf'] : null;
        $this->src         = (!empty($data['src'])) ? $data['src'] : null;

        $this->id_para      = (!empty($data['id_para'])) ? $data['id_para'] : null;
        $this->id_ortho      = (!empty($data['id_ortho'])) ? $data['id_ortho'] : null;
        $this->id_rule      = (!empty($data['id_rule'])) ? $data['id_rule'] : null;
        $this->id_formula      = (!empty($data['id_formula'])) ? $data['id_formula'] : null;
        $this->id_comment      = (!empty($data['id_comment'])) ? $data['id_comment'] : null;

     }
 }
 ?>
 
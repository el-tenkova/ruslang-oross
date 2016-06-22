<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Contents\Model;

 class Tetragramm
 {
    public $id;
    public $word;
    public $start;
    public $len;
    public $title;
    public $segment;
    public $number;

    public $id_article;
    public $art_count;

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

        $this->word         = (!empty($data['tetragramm'])) ? $data['tetragramm'] : null;

        $this->id_article   = (!empty($data['id_article'])) ? $data['id_article'] : null;
        $this->art_count    = (!empty($data['art_count'])) ? $data['art_count'] : null;
        
     }
 }
 ?>
 
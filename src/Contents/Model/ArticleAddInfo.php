<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Contents\Model;

 class ArticleAddInfo
 {
    public $id;
    public $id_article;
    public $id_src;    
    public $text;

    public function exchangeArray($data)
    {
        $this->id           = (!empty($data['id'])) ? $data['id'] : null;
        $this->id_article   = (!empty($data['id_article'])) ? $data['id_article'] : null;
        $this->id_src   = (!empty($data['id_src'])) ? $data['id_src'] : null;
        $this->text   = (!empty($data['text'])) ? $data['text'] : null;
     }
 }
 ?>
 
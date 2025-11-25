<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Model;

 class Changes
 {
    public $id;
    public $id_article;
    public $title;
    public $text;
    public $src;
    public $dic;
    public $action;
    public $status;
    public $chd;
    public $username;
    //
    public $src_art;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->id_article = (!empty($data['id_article'])) ? $data['id_article'] : null;
        $this->title = (!empty($data['title'])) ? $data['title'] : null;
        $this->text = (!empty($data['text'])) ? $data['text'] : null;
        $this->src = (!empty($data['src'])) ? $data['src'] : null;
        $this->dic = (!empty($data['dic'])) ? $data['dic'] : null;
        $this->action = (!empty($data['action'])) ? $data['action'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
        $this->chd = (!empty($data['chd'])) ? $data['chd'] : null;
        $this->username = (!empty($data['username'])) ? $data['username'] : null;
        //
        $this->src_art = (!empty($data['src_art'])) ? $data['src_art'] : null;
     }
 }
 ?>
 
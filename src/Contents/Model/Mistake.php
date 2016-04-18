<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Contents\Model;

 class Mistake
 {
    public $id;
    public $id_mistake;
    public $mistake;
    public $word;

    public function exchangeArray($data)
    {
        $this->id           = (!empty($data['id'])) ? $data['id'] : null;
        $this->id_mistake   = (!empty($data['id_mistake'])) ? $data['id_mistake'] : null;
        $this->mistake      = (!empty($data['mistake'])) ? $data['mistake'] : null;
        $this->word         = (!empty($data['word'])) ? $data['word'] : null;
     }
 }
 ?>
 
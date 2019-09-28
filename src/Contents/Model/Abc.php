<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Contents\Model;

 class Abc
 {
    public $id_first;
    public $id_last;
    public $a_first;
    public $a_last;

    public function exchangeArray($data)
    {
        
        $this->id_first		= (!empty($data['id_first'])) ? $data['id_first'] : null;
        $this->id_last     = (!empty($data['id_last'])) ? $data['id_last'] : null;
        $this->a_first      = (!empty($data['a_first'])) ? $data['a_first'] : null;
        $this->a_last       = (!empty($data['a_last'])) ? $data['a_last'] : null;
     }
 }
 ?>
 
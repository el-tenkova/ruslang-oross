<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Contents\Model;

 class State
 {
    public $state;
    public $last;
    public $result;

    public function exchangeArray($data)
    {
        
        $this->state    = (!empty($data['state'])) ? $data['state'] : null;
        $this->last      = (!empty($data['last'])) ? $data['last'] : null;
        $this->result   = (!empty($data['result'])) ? $data['result'] : null;
     }
 }
 ?>
 
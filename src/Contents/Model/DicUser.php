<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Contents\Model;

 class DicUser
 {
    public $username;
    public $password;
    public $fullname;

    public function exchangeArray($data)
    {
        $this->username           = (!empty($data['username'])) ? $data['username'] : null;
        $this->password        = (!empty($data['password'])) ? $data['password'] : null;
        $this->fullname        = (!empty($data['fullname'])) ? $data['fullname'] : null;
     }
 }
 ?>
 
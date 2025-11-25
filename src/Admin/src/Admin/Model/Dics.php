<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Model;

 class Dics
 {
    public $id;
    public $name;
    public $path;
    public $status;
    public $chd;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->path = (!empty($data['path'])) ? $data['path'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
        $this->chd = (!empty($data['chd'])) ? $data['chd'] : null;
     }
 }
 ?>
 
<?php

namespace Admin\Form;

use Zend\Form\Form;

class ArticleAddInfoForm extends Form
{
    public function __construct()
    {
        // we want to ignore the name passed
        parent::__construct('articleaddinfoform');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'sources',
            'attributes' => array ('style' => 'width: 560px', 'class' => 'info-src'),
            'options' => array(
          )));
         $this->add(array(
            'name' => 'info',
            'attributes' => array(
                'type'  => 'textarea',
	            'id' => 'info',
	            'value' =>"",
            ),
        ));
        $this->add(array(
            'name' => 'addinfo',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Сохранить',
                'id' => 'addinfobutton',
                'class' => 'add-info',
            ),
        ));
        $this->add(array(
            'name' => 'delinfo',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Удалить',
                'id' => 'delinfobutton',
                'class' => 'del-info',
            ),
        ));
    }
}
?>
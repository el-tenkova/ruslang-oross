<?php

namespace Admin\Form;

use Zend\Form\Form;

class AddUserForm extends Form
{
    public function __construct()
    {
        // we want to ignore the name passed
        parent::__construct('adduserform');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'username',
            'attributes' => array(
                'type'  => 'text',
	            'id' => 'username',
            ),
        ));
        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type'  => 'text',
	            'id' => 'password',
            ),
        ));
        $this->add(array(
            'name' => 'fullname',
            'attributes' => array(
                'type'  => 'text',
	            'id' => 'fullname',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Добавить',
                'id' => 'submitbutton',
            ),
         ));
        $this->add(array(
            'name' => 'delete',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Удалить',
                'id' => 'deletebutton',
            ),
        ));
    }
}
?>
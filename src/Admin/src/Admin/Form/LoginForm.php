<?php

namespace Admin\Form;

use Zend\Form\Form;

class LoginForm extends Form
{
    public function __construct($username = null)
    {
        // we want to ignore the name passed
        parent::__construct('loginform');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'username',
            'attributes' => array(
                'type'  => 'text',
	            'id' => 'username',
	            'value' => $username,
            ),
        ));
        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type'  => 'password',
	            'id' => 'password',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Войти',
                'id' => 'submitbutton',
            ),
        ));
    }
}
?>
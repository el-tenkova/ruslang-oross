<?php

namespace Admin\Form;

use Zend\Form\Form;
use Application\Model\DicUserTable;

class LogoutForm extends Form
{
    public function __construct($fullname)
    {
        // we want to ignore the name passed
        parent::__construct('logoutform');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'username',
            'attributes' => array(
                'type'  => 'text',
	            'id' => 'username',
            ),
            'options'    => array(
    	            'label' => $fullname,
                    'label_attributes' => array(
                            'class'  => 'control-label'),
        )));
        $this->add(array(
            'name' => 'logout',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Выйти',
                'id' => 'logoutbutton',
            ),
        ));
    }
}
?>
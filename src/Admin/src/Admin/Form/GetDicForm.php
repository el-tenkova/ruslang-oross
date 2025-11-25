<?php

namespace Admin\Form;

use Zend\Form\Form;

class GetDicForm extends Form
{
    public function __construct($username = null)
    {
        // we want to ignore the name passed
        parent::__construct('getdicform');
        $this->setAttribute('method', 'post');
/*        $this->add(array(
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
        )); */
        $this->add(array(
            'name' => 'oross',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Запросить ОРОСС',
                'id' => 'orossbutton',
            ),
        ));
        $this->add(array(
            'name' => 'ros',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Запросить РОС',
                'id' => 'rosbutton',
            ),
        ));
    }
}
?>
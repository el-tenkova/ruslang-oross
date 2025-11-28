<?php

namespace Admin\Form;

use Zend\Form\Form;

class EditPageForm extends Form
{
    public function __construct($text)
    {
        // we want to ignore the name passed
        parent::__construct('editpageform');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'text',
            'attributes' => array(
                'type'  => 'textarea',
	            'id' => 'text',
	            'value' =>$text,
	            'cols' => 150,
	            'rows' => 20,
            ),
        ));
        $this->add(array(
            'name' => 'pageok',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Сохранить',
                'id' => 'submitbutton',
            ),
         ));
    }
}
?>
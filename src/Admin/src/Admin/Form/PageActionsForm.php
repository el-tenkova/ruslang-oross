<?php

namespace Admin\Form;

use Zend\Form\Form;

class PageActionsForm extends Form
{
    public function __construct($idx)
    {
        // we want to ignore the name passed
        parent::__construct('pageactionsform');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'id_page',
            'attributes' => array(
                'type'  => 'text',
	            'id' => 'id_page',
            ),
            'options'    => array(
    	            'label' => $idx,
                    'label_attributes' => array(
                            'class'  => 'control-label'),
        )));
        $this->add(array(
            'name' => 'del_rule',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Удалить',
                'id' => 'delbutton',
            ),
        ));
        $this->add(array(
            'name' => 'edit_page',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Редактировать',
                'id' => 'editbutton',
            ),
        ));
    }
}
?>
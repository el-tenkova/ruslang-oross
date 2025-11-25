<?php

namespace Admin\Form;

use Zend\Form\Form;

class ArticleActionsForm extends Form
{
    public function __construct($idx)
    {
        // we want to ignore the name passed
        parent::__construct('articleactionsform');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'id_article',
            'attributes' => array(
                'type'  => 'text',
	            'id' => 'id_article',
            ),
            'options'    => array(
    	            'label' => $idx,
                    'label_attributes' => array(
                            'class'  => 'control-label'),
        )));
        $this->add(array(
            'name' => 'delete',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Удалить',
                'id' => 'deletebutton',
            ),
        ));
        $this->add(array(
            'name' => 'edit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Редактировать',
                'id' => 'editbutton',
            ),
        ));
        $this->add(array(
            'name' => 'addinfo',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Дополнить',
                'id' => 'addinfobutton',
            ),
        ));
    }
}
?>
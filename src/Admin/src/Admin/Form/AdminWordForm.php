<?php

namespace Admin\Form;

use Zend\Form\Form;

class AdminWordForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('searchword');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'word',
            'attributes' => array(
                'type'  => 'text',
	            'id' => 'word',
            ),
            'options' => array(
                'label' => 'Поиск слова',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Поиск',
                'id' => 'submitbutton',
            ),
        ));
        $this->add(array(
			'type'  => 'checkbox',
            'name' => 'title_check',
    		'options' => array(
	        	'label' => '  Искать в заголовках статей',
		        'use_hidden_element' => false,
	    	    'checked_value' => 'yes',
	        	'unchecked_value' => 'no'
    		),
            'attributes' => array(
                'value' => 'yes',
            ),
        ));
        $this->add(array(
			'type'  => 'checkbox',
            'name' => 'text_check',
    		'options' => array(
	        	'label' => '  Искать в тексте статей',
		        'use_hidden_element' => false,
	    	    'checked_value' => 'yes',
	        	'unchecked_value' => 'no'
    		),
            'attributes' => array(
                'value' => 'yes',
            ),
        )); 
        $this->add(array(
            'name' => 'artnew',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Новая статья',
                'id' => 'newartbutton',
            ),
        ));
        
    }
}
?>
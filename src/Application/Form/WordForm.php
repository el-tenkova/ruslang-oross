<?php

namespace Application\Form;

use Zend\Form\Form;
use Contents\Model\HistoricTables;

class WordForm extends Form
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
			'type'  => 'checkbox',
            'name' => 'tut_check',
    		'options' => array(
	        	'label' => '  Искать в справочнике',
		        'use_hidden_element' => false,
	    	    'checked_value' => 'yes',
	        	'unchecked_value' => 'no'
    		),
            'attributes' => array(
                'value' => 'yes',
            ),
        ));
/*        $this->add(array(
			'type'  => 'radio',
            'name' => 'search_part',
	        'options' => array(
	            'value_options' => array(
	                array('value' => 'word', 'label' => 'Искать слово целиком', 'selected' => true),
	                array('value' => 'part', 'label' => 'Искать часть слова', 'selected' => false),
	            ),
			),
        )); */
        
/*	     $this->add(array(
	             'type' => 'Zend\Form\Element\Select',
	             'name' => 'historic',
	             'options' => array(
	                     'label' => 'Languages',
	                     'empty_option' => '-- Выберите --',
	                     'value_options' => $hist,
	             )
	     ));        
        $this->add(array(
            'name' => 'submithist',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Поиск',
                'id' => 'submithistoric',
            ),
        )); */
    }
}
?>
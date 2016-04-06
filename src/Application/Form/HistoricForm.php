<?php

namespace Application\Form;

use Zend\Form\Form;
use Contents\Model\HistoricTables;

class HistoricForm extends Form
{
    public function __construct($name = null, $hist)
    {
        // we want to ignore the name passed
        parent::__construct('histsearch');
        $this->setAttribute('method', 'post');
/*        $this->add(array(
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
        )); */
	     $this->add(array(
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
        ));
    }
}
?>
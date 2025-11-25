<?php

namespace Admin\Form;

use Zend\Form\Form;

class ArticleNewForm extends Form
{
    public function __construct()
    {
        // we want to ignore the name passed
        parent::__construct('articlenewform');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'article',
            'attributes' => array(
                'type'  => 'textarea',
	            'id' => 'article',
            ),
        ));
        $this->add(array(
            'type' => 'radio',
            'name' => 'dic',
            'options' => array(
                'value_options' => array(
                        array(
                        'value' => '0',
                        'label' =>  '     Статья ОРОСС'
                        ),
                        array(
                        'value' =>'1',
                        'label' => '     Статья РОС'
                        ),
                ),
            ),
            'attributes' => array(
                'value' => '0' 
            )            
        ));        
        $this->add(array(
            'name' => 'newok',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Готово',
                'id' => 'newokbutton',
            ),
        ));
    }
}
?>
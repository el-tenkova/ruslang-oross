<?php

namespace Admin\Form;

use Zend\Form\Form;

class ArticleEditForm extends Form
{
    public function __construct($src)
    {
        // we want to ignore the name passed
        parent::__construct('articleeditform');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'article',
            'attributes' => array(
                'type'  => 'textarea',
	            'id' => 'article',
	            'value' =>$src,
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
            'name' => 'editok',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Готово',
                'id' => 'editokbutton',
            ),
        ));
/*        $this->add(array(
            'name' => 'preview',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Предварительный просмотр',
                'id' => 'previewbutton',
            ),
        )); */
        
    }
}
?>
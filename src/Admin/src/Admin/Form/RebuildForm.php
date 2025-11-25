<?php

namespace Admin\Form;

use Zend\Form\Form;

class RebuildForm extends Form
{
    public function __construct($username = null)
    {
        // we want to ignore the name passed
        parent::__construct('rebuildform');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'rebuild',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Запустить индексацию',
                'id' => 'rebuildbutton',
            ),
        ));
    }
}
?>
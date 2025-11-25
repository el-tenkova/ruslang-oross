<?php

namespace Admin\Form;

use Zend\Form\Form;

class ActionCancelForm extends Form
{
    public function __construct($idx)
    {
        parent::__construct('actioncancelform');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'cancel',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Отменить',
                'id' => 'cancelbutton',
            ),
        ));
    }
}
?>
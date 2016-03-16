<?php

namespace Application\Form;

use Zend\Form\Form;

class DownloadForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        error_log("DownloadForm ctor");
        parent::__construct('rtf');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'rtfdown',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Скачать',
                'id' => 'rtfdownload',
            ),
        ));
    }
}
?>
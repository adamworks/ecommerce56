<?php

namespace main\Tools\Option;

use Core\Foundation\Form\Form;

class OptionForm extends Form
{
    protected $validationRules = [
        'value' => 'required|max:255',
    ];
}
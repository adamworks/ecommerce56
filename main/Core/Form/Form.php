<?php

namespace Core\Foundation\Form;

use Illuminate\Support\Facades\Validator;
use Core\Foundation\Exceptions\NoValidationRulesFoundException;

class Form
{
    protected $data;
    protected $validationRules;
    protected $validationMarkdowns;
    protected $validationMessages = [];
    protected $validator;
    protected $valid = null;

    public function __construct($data = null, $validationMarkdowns = [])
    {
        if (is_null($data)) {
            $this->data = [];
        } else {
            $this->data = $data;
        }

        $this->validationMarkdowns = $validationMarkdowns;
    }

    public function getData()
    {
        return $this->data;
    }

    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return $default;
    }

    public function isValid()
    {
        if (!is_null($this->valid)) {
            return $this->valid;
        }

        $this->beforeValidation();

        if ( ! isset($this->validationRules)) {
            throw new NoValidationRulesFoundException('no validation rules found in class ' . get_called_class());
        }

        $this->validator = Validator::make($this->getData(), $this->getPreparedRules(), $this->getPreparedMessages());

        $valid = $this->validator->passes();

        $this->valid = $valid;

        return $valid;
    }

    public function validate()
    {
        if (!$this->isValid()) {
            throw new FormValidationException($this->getErrors());
        }
    }

    public function getValidData()
    {
        $this->validate();

        return $this->getData();
    }

    public function getErrors()
    {
        return $this->validator->errors();
    }

    protected function getPreparedRules()
    {
        if (empty($this->validationMarkdowns)) {
            return $this->validationRules;
        }

        $rules = $this->validationRules;
        $markdowns = $this->validationMarkdowns;

        foreach ($rules as &$rule) {
            foreach ($markdowns as $key => $val) {
                $rule = str_replace('{'.$key.'}', $val, $rule);
            }
        }

        return $rules;
    }

    protected function getPreparedMessages()
    {
        return $this->validationMessages;
    }

    protected function beforeValidation() {}
}
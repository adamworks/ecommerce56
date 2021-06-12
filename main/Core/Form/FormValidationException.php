<?php

namespace Core\Foundation\Form;

class FormValidationException extends \Exception
{
	protected $errors = [];

	public function __construct($errors)
	{
		//dd($errors);
		parent::__construct('Invalid form', 403);

		$this->errors = $errors;
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function getResponse($codeMessage = '',$dataResponse='')
	{
		$dataResponse = !empty($dataResponse) ? $dataResponse : $this->getErrors();

		return formValidatorExeptionResponse($codeMessage, $dataResponse);
	}
}
<?php

namespace Core\Support\Option\Exception;

class CodePushException extends \Exception
{
	private $_options;
	public function __construct($message, 
                                $code = 400, 
                                Exception $previous = null, 
                                $options = array('params'))
	{
		parent::__construct($message,$code,$previous);
		$this->_options = $options;
	}

	public function getResponse()
	{
		$content = [
            'code' => 400,
            'code_message' => trans('version.codepush'),
            'code_type' => 'badRequest',
            'data' => $this->_options
        ];

		return response($content, $status = 400);
	}
}
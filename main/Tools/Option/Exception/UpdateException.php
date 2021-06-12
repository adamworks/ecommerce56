<?php

namespace Core\Support\Option\Exception;

class UpdateException extends \Exception
{
	private $_options;
	private $_code;

	public function __construct($message, 
                                $code = 400, 
                                Exception $previous = null, 
                                $options = array('params'))
	{
		parent::__construct($message,$code,$previous);
		$this->_options = $options;
		$this->_code = $code;
	}

	public function getResponse()
	{
		$content = [
            'code' => $this->_code,
            'code_message' => trans('version.update')
        ];

        if($this->_code == 400)
        {
        	$content['code_type'] = 'badRequest';
        	$content['data'] = $this->_options;
        }

		return response($content, $this->_code);
	}
}
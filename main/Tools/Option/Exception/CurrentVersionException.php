<?php

namespace Core\Support\Option\Exception;

class CurrentVersionException extends \Exception
{
	public function __construct()
	{
		parent::__construct('Request', 200,);
	}

	public function getResponse($data)
	{
		$content = [
            'code' => 200,
            'code_message' => trans('version.current'),
            'code_type' => 'goodReqeust',
            'data' => $data,
        ];

		return response($content, $status =200);
	}
}
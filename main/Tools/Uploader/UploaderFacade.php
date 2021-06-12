<?php

namespace main\Tools\Uploader;

use Illuminate\Support\Facades\Facade;

class UploaderFacade extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'main.tools.uploader';
	}
}
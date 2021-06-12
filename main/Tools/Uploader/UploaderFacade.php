<?php

namespace Core\Support\Uploader;

use Illuminate\Support\Facades\Facade;

class UploaderFacade extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'core.support.uploader';
	}
}
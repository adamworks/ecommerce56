<?php

namespace Core\Support\Formatter;

use Illuminate\Support\Facades\Facade;

class FormatterFacade extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'core.support.formatter';
	}
}
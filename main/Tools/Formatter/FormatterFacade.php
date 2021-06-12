<?php

namespace main\Tools\Formatter;

use Illuminate\Support\Facades\Facade;

class FormatterFacade extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'main.tools.formatter';
	}
}
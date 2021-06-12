<?php

namespace main\Tools\Option;

use Illuminate\Support\Facades\Facade;

class OptionFacade extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'main.Tools.option';
	}
}
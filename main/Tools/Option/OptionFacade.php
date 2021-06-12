<?php

namespace Core\Support\Option;

use Illuminate\Support\Facades\Facade;

class OptionFacade extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'core.support.option';
	}
}
<?php

namespace main\Tools\Option;

use Illuminate\Support\Facades\Cache;
use main\Core\EloquentRepository;

class OptionRepository extends EloquentRepository
{
	public function __construct(Option $model)
	{
		parent::__construct($model);
	}

	public function getByKey($key)
	{
		// $cacheKey = $this->getCacheKey($key);
		// if (Cache::has($cacheKey)) {
		// 	return Cache::get($cacheKey);
		// }

		$option = $this->model->where('meta_key', $key)->first();

		// if (!is_null($option)) {
		// 	Cache::forever($cacheKey, $option);
		// }

		return $option;
	}

	protected function storeEloquentModel($model)
    {
    	$cacheKey = $this->getCacheKey($model->meta_key);
		Cache::forget($cacheKey);

        return parent::storeEloquentModel($model);
    }

	private function getCacheKey($key)
	{
		return 'option.'.$key;
	}
}
<?php

namespace main\Tools\Option;

use main\Tools\Option\Exception\CodePushException;
use main\Tools\Option\Exception\UpdateException;
use main\Tools\Option\Exception\CurrentVersionException;

class OptionService
{
	protected $options;

	public function __construct(OptionRepository $options)
	{
		$this->options = $options;
	}

	public function get($key, $default = null)
	{
		$option = $this->options->getByKey($key);

		if (is_null($option)) {
			
			$this->set($key, $default);

			return $default;
		}

		return $option->meta_value;
	}

	public function set($key, $value)
	{
		$option = $this->options->getByKey($key);

		if (is_null($option)) {
			$option = $this->options->getNew();
			$option->meta_key = $key;
		}

		//set current version from front dev.

		if($key=='versioning' &&  ($option->meta_value!=null || $option->meta_value!="") ){
			$val = $this->my_version_compare($value,$option->meta_value);

			if($val==2){
				$value = $value; 
			} else if($val==0){
				//return $value;
				throw new CurrentVersionException();
			} else if($val==1){
				throw new CodePushException();
			} else if($val==-1){
				throw new UpdateException();
			} 
			
		}

		$option->meta_value = is_array($value) ? json_encode($value) : $value;

		$this->options->save($option);
		return $option;

	}

	public function setArray($value)
	{
		foreach($value['data'] as $key => $value){
			$option = $this->options->getByKey($key);
			
			if (is_null($option)) {
				$option = $this->options->getNew();
				$option->meta_key = $key;
			}

			$option->meta_value = is_array($value) ? json_encode($value) : $value;

			$this->options->save($option);
		}
	}

    public function showMaxShipping()
    {
        return $this->options->getByKey('shippingCost');
    }

    public function showMaxDeliveryDate()
    {
        return $this->options->getByKey('maxDeliveryDate');
    }

    public function showDurationUnit()
    {
        return $this->options->getByKey('durationUnit');
    }

    public function showDurationTime()
    {
        return $this->options->getByKey('durationTime');
    }

    public function showMinTrx()
    {
        return $this->options->getByKey('minTrxFreeShippingCost');
    }

    private function my_version_compare($new_version, $old_version, $only_minor = false){

    	$new = explode(".", $new_version);
    	$old = explode(".", $old_version);

    	$new_majorminor = $new[0] . "." .$new[1];
    	$old_majorminor = $old[0] . "." .$old[1];
    	
    	/** compare new major minor */
    	if(version_compare($new_majorminor, $old_majorminor,"=")){
    		$result = $new[2]-$old[2];
    		
    		if ($result > 0)
				$value = 2;
			else if ($result == 0)
				$value = 0;
			else if($result < 0)
				$value = 1;
				
    	} else if(version_compare($new_majorminor, $old_majorminor,">")){
    		$value = 2;
    	} else {
    		$value = -1;
    	}

    	return $value;
	}

    private function new_version_compare($s1,$s2){
	    $sa1 = explode(".",$s1);
	    $sa2 = explode(".",$s2);
	    if(($sa2[2]-$sa1[2])<0) //less
	        return 1;
	    if(($sa2[2]-$sa1[2])==0) //same
	        return 0;
	    if(($sa2[2]-$sa1[2])>0) //bigger
	        return -1;

	    $value = $option->meta_value >= $value ? $option->meta_value : $value;
	}
}
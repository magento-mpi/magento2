<?php

class Mage_Core_Model
{
	public static function getModelClass($model, $class='', $arguments=array())
	{
	    $className = '';
	    if (Mage::getConfig('/')) {
	        $className = (string)Mage::getConfig('/')->global->models->$model->class;
	    }	    

		if (empty($className)) {
			Mage::exception('No model is defined: '.$model);
		}
		
		if (''!==$class) {
			$className .= '_'.str_replace(' ', '_', ucwords(str_replace('_', ' ', strtolower($class))));
		}

		return new $className($arguments);
	}
}
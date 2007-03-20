<?php

class Mage_Core_Model
{
    static protected $_models = array();
    
    public static function addModel($name, $class)
    {
        self::$_models[$name] = $class;
    }
    
	public static function getModelClass($model, $class='', $arguments=array())
	{
	    $className = '';
	    if (Mage::getConfig('/')) {
	        $className = (string)Mage::getConfig('/')->global->models->$model->class;
	    }	  

	    if (empty($className)) {
    	    if (!isset(self::$_models[$model])) {
    			Mage::exception('No model is defined: '.$model);
    		} else {
    		    $className = self::$_models[$model];
    		}
	    }
		
		if (''!==$class) {
			$className .= '_'.str_replace(' ', '_', ucwords(str_replace('_', ' ', strtolower($class))));
		}

		return new $className($arguments);
	}
	
	public static function runModelMethod($module, $modelName, $method, $params = array())
	{
	    $model = self::getModelClass($module, $modelName);
	    $res = $model->$method($params);
	    return $res;
	}
}

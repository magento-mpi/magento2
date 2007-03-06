<?php

class Mage_Core_Model
{
    static private $_models = array();
	
    public static function setModel($name, $type='')
    {
        self::$_models[$name] = $type;
    }
    
    public static function loadModelsConfig($config)
    {
        foreach($config as $comp=>$type) {
            self::setModel($comp, $type);
        }        
    }

	public static function getModelClass($model, $class='', $arguments=array())
	{
		if (!isset(self::$_models[$model])) {
			Mage::exception('No model is defined: '.$model);
		}
		
		$className = self::$_models[$model];
		
		if (''!==$class) {
			$className .= '_'.str_replace(' ', '_', ucwords(str_replace('_', ' ', strtolower($class))));
		}

		return new $className($arguments);
	}
}
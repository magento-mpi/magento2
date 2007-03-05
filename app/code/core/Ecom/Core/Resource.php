<?php

/**
 * Manages Ecom global resources
 * 
 * @todo refactor to use extendable objects as resources
 * @author Moshe Gurvich <moshe@varien.com>
 */
class Ecom_Core_Resource 
{
    /**
     * Resource types
     *
     * @var array
     */
    private static $_types = null;
    
    
    /**
     * Collection of resources (connections to DBs, etc)
     *
     * @var array
     */
    static private $_resources = array();
    
    /**
     * Resource factory
     * 
     * Returns resource object based on configuration array supplied
     *
     * @author Moshe Gurvich <moshe@varien.com>
     * @param array $config
     * @return resource
     */
    static public function loadResource($name, array $config=null)
    {
        if ($class = self::getType($config['type'])) {
            $resource = self::setResource($name, new $class($config));
        } else {
            $resource = null;
        }
        return $resource;
    }
        
    /**
     * Add named resource reference
     *
     * @param string $name
     * @param resource $_resource
     */
    public static function setResource($name, $resource)
    {
        self::$_resources[$name] = $resource;
        return self::$_resources[$name];
    }
    
    /**
     * Retrieve named resource
     *
     * @param string $name
     * @return resource || false
     */
    public static function getResource($name='')
    {
        if ($name=='') {
            return self::$_resources;
        }
        
        if (!isset(self::$_resources[$name])) {
            Ecom::exception('Non existing resource requested: '.$name);
        }
        
        return self::$_resources[$name];
    }
    
    static public function setType($config, $class='')
    {
        self::$_types[$config] = $class;
    }
    
    static public function getType($name='')
    {
        if (''===$name) {
            return self::$_types;
        } else if (!empty(self::$_types[$name])) {
            return self::$_types[$name];
        } else {
            return false;
        }
    }
    
    static public function loadTypesConfig(Zend_Config $config)
    {
        foreach($config as $type=>$class) {
            self::setType($type, $class);
        }
    }
    
    /**
     * Load resources from Zend_Config object
     *
     * @author Moshe Gurvich <moshe@varien.com>
     * @param Zend_Config_Ini $config
     */
    static public function loadResourcesConfig(Zend_Config $config)
    {
        $refs = array();
        
        foreach ($config as $name=>$resConfig) {
            if (is_string($resConfig)) {
                // found named link to existing definition
                if (isset($refs[$resConfig])) {
                    $refs[$name] = $refs[$resConfig];
                } elseif (isset(self::$_resources[$resConfig])) {
                    $refs[$name] = $resConfig;
                }
            } else if ($resConfig instanceof Zend_Config && !empty($resConfig->type) && is_string($resConfig->type)) { 
                // found resource definition
                self::loadResource($name, $resConfig->asArray());
            }
        }
        
        if (!empty($refs)) {
            foreach ($refs as $name=>$ref) {
                self::setResource($name, self::getResource($ref));
            }
        }    
    }
    
    static public function loadEntitiesConfig($config)
    {
        $refs = array();
        
        foreach ($config as $name=>$entConfig) {
            if (is_string($entConfig)) {
                // found named link to existing definition
                if (isset($refs[$entConfig])) {
                    $refs[$name] = $refs[$entConfig];
                } elseif (isset(self::$_resources[$entConfig])) {
                    $refs[$name] = $entConfig;
                }
            } else {
                self::getResource($name)->loadEntitiesArray($entConfig);
            }
        }

        if (!empty($refs)) {
            foreach ($refs as $name=>$ref) {
                self::getResource($name)->loadEntitiesArray($config->$ref);
            }
        }    
    }
}
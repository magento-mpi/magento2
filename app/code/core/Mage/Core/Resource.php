<?php

/**
 * Manages Mage global resources
 * 
 * @todo refactor to use extendable objects as resources
 * @author Moshe Gurvich <moshe@varien.com>
 */
class Mage_Core_Resource 
{
    /**
     * Collection of resources (connections to DBs, etc)
     *
     * @var array
     */
    static private $_resources = array();

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
            $resource = Mage::getConfig('/')->global->resources->$name;
            $rType = (string)$resource->connection->type;
            $rTypeClass = (string)Mage::getConfig('/')->global->resourceTypes->$rType->class;
            self::$_resources[$name] = new $rTypeClass($resource);
            if (!isset(self::$_resources[$name])) {
                Mage::exception('Non existing resource requested: '.$name);
            }        
        }
        
        return self::$_resources[$name];
    }
    
    static public function getType($name='')
    {
        if (''===$name) {
            return Mage::getConfig('/')->global->resourceTypes;
        } else {
            return Mage::getConfig('/')->global->resourceTypes->$name;
        }
    }
    
    static public function getEntity($resource, $entity='')
    {
        $entities = Mage::getConfig('/')->global->resources->$resource->entities;
        if (''===$entity) {
            return $entities;
        } else {
            return $entities->$entity;
        }
    }
}
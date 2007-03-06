<?php

class Mage_Catalog_Price_Rule
{
    protected static $_conditionTypes = array();
    protected static $_actionTypes = array();
    
    public static function addConditionType($type, $className) 
    {
        self::$_conditionTypes[$type] = $className;
    }
    
    public static function loadConditionTypesConfig($config)
    {
        foreach ($config as $type=>$className) {
            self::addConditionType($type, $className);
        }
    }
        
    public static function addActionType($type, $className) 
    {
        self::$_actionTypes[$type] = $className;
    }
    
    public static function loadActionTypesConfig($config)
    {
        foreach ($config as $type=>$className) {
            self::addActionType($type, $className);
        }
    }
    
    public static function getByProductId($productId)
    {
        
    }
}
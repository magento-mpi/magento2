<?php

class WebService_Helper_Data
{
    protected static $_data = array();

    public static function set($key, $value)
    {
        self::$_data[$key] = $value;
    }

    public static function get($key)
    {    
        return self::$_data[$key];
    }

    public static function transformToClass($path)
    {
        return str_replace('/', '_', $path);
    }

    public static function transformPath($path)
    {
        return str_replace( array('/', "\\"), DS, $path);
    }

    public static function getDataFromXmlFile($xmlPath)
    {
        $xml = simplexml_load_file($xmlPath);
        return WebService_Helper_Xml::simpleXMLToArray($xml);        
    }
}
?>

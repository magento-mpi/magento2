<?php

class Webservice_Helper_Reflection
{
    public static function Call($className, $methodName)
    {
        $obj = new stdClass();
        $refType = new ReflectionClass($className);
        $refMethod = $refType->getMethods($methodName);
        return $refMethod->invoke($obj);
    }    
}
?>

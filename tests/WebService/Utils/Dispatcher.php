<?php

class WebService_Utils_Dispatcher
{
    protected static $_modelName;

    public static function setModel($modelName)
    {
        self::$_modelName = $modelName;
    }
    
    public static function dispatch($methodName, $type = null)
    {
        if( $type === null ) {
            $type = WebService_Helper_Data::get('Suite');
        }
        
        $path = WebService_Helper_Data::get('pathToImplementation').'/'.$type.'/'.self::$_modelName;
        $className = WebService_Helper_Data::transformToClass($path);
        $method = new ReflectionMethod($className, $methodName);
        $params = array(
            "xmlPath" => WebService_Helper_Data::get('pathToConfig').'/'.self::$_modelName.'/'.ucfirst($methodName).'.xml'
        );
        $result = $method->invokeArgs(new $className(), $params);
        return $result;
    }
}
?>

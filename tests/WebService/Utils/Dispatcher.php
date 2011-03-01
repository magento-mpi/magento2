<?php

class WebService_Utils_Dispatcher
{
    protected static $_modelName;

    public static function setModel($modelName)
    {
        self::$_modelName = $modelName;
    }
    
    public static function dispatch($methodName, $suite = null)
    {
        if( $suite === null ) {
            $suite = WebService_Helper_Data::get('Suite');
        }
        
        $path = WebService_Helper_Data::get('pathToImplementation').'/'.$suite.'/'.self::$_modelName;
        $className = WebService_Helper_Data::transformToClass($path);
        $class = new $className();
        $xmlPath = WebService_Helper_Data::transformPath(WebService_Helper_Data::get('pathToConfig').DS.self::$_modelName.DS.ucfirst($methodName).'.xml');
        if (!is_file($xmlPath)) {
            $xmlPath = null;
        }
        return call_user_func(array($class, $methodName), $xmlPath );
    }
}
?>

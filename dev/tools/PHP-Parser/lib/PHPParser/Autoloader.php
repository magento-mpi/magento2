<?php

/**
 * @codeCoverageIgnore
 */
class PHPParser_Autoloader
{
    /**
    * Registers PHPParser_Autoloader as an SPL autoloader.
    */
    public static function register()
    {
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register([__CLASS__, 'autoload']);
    }

    /**
    * Handles autoloading of classes.
    *
    * @param string $class A class name.
    */
    public static function autoload($class)
    {
        if (0 !== strpos($class, 'PHPParser')) {
            return;
        }

        $file = dirname(dirname(__FILE__)) . '/' . strtr($class, '_', '/') . '.php';
        if (is_file($file)) {
            require $file;
        }
    }
}

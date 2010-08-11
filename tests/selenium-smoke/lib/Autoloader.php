<?php
/**
 * PHP Class Autoloader
 */
class Autoloader {

    /**
     * Autoloader instance
     *
     * @var Autoloader
     */
    protected static $_loaderInstance = null;

    /**
     * Singleton pattern implementation
     * Initiates the Autoloader instance
     */
    public static function init()
    {
       if (null === self::$_loaderInstance) {
           self::$_loaderInstance = new self;
       }
    }

    /**
     * Constructor
     * Registers the autoloader handler
     */
    private function  __construct()
    {
        spl_autoload_register(array(get_class($this), 'autoload'));
    }

    /**
     * Autoloader handler implementation
     *
     * @param string $className
     * @return boolean
     */
    public function autoload($className) {
        if (0 === strpos($className, 'Test_')) {
            $className = str_ireplace('Test_', 'tests_', $className);
        }

        $classFile = str_replace(' ', DS, ucwords(str_replace('_', ' ', $className)));
        $classFile = $classFile . '.php';
        return include $classFile;
    }
}

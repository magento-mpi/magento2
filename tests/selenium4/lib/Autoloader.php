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
     * Directories that allow autoloading
     * If an item index is associative, the key is a Model prefix
     *
     * @var array
     */
    protected static $_directories = array(
        'Model' => 'models',
        'tests',
        'lib',
    );

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
        foreach (self::$_directories as $prefix => $dir) {
            set_include_path(implode(PATH_SEPARATOR, array(
                realpath(BASE_DIR . DS . $dir),
                get_include_path(),
            )));
        }

        spl_autoload_register(array(get_class($this), 'autoload'));
    }

    /**
     * Autoloader handler implementation
     *
     * @param string $className
     * @return boolean
     */
    public function autoload($className) {
        foreach (self::$_directories as $prefix => $dir) {
            if (is_string($prefix)) {
                if (0 === strpos($className, $prefix . '_')) {
                    $className = str_ireplace($prefix . '_', $dir . '_', $className);
                    break;
                }
            }
        }

        $classFile = str_replace(' ', DS, ucwords(str_replace('_', ' ', $className)));
        $classFile = $classFile . '.php';

        $result = include_once $classFile;

        // echo "[Autoloading: {$classFile}, result: {$result}]\n";

        return $result;
    }
}

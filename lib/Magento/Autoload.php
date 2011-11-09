<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Loader
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Classes auto load with files map support
 */
class Magento_Autoload
{
    /**
     * Namespaces separator
     */
    const NS_SEPARATOR = '\\';

    /**
     * Singleton
     *
     * @var Magento_Autoload
     */
    protected static $_instance;

    /**
     * Association between class names and files
     *
     * @var array
     */
    protected $_filesMap = array();

    /**
     * Base code directory
     *
     * @var string
     */
    protected $_baseDir;

    /**
     * Class constructor that automatically register auto load
     */
    protected function __construct()
    {
        spl_autoload_register(array($this, 'autoload'));
        $this->_baseDir = realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR;
    }

    /**
     * Ability to use loader sith singleton implementation
     *
     * @return Magento_Autoload
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new Magento_Autoload();
        }
        return self::$_instance;
    }

    /**
     * Check if class file exists
     *
     * @param string $class
     * @return bool
     */
    public function classExists($class)
    {
        if (class_exists($class, false)) {
            return true;
        }

        if (isset($this->_filesMap[$class]) && file_exists($this->_filesMap[$class])) {
            return true;
        }

        $classFile = $this->_getClassFile($class);
        foreach (explode(PATH_SEPARATOR, get_include_path()) as $path) {
            $fileName = $path . DIRECTORY_SEPARATOR . $classFile;
            if (file_exists($fileName)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Auto load class file
     *
     * @param string $class class name
     */
    public function autoload($class)
    {
        if (isset($this->_filesMap[$class])) {
            $classFile = $this->_baseDir . $this->_filesMap[$class];
        } else {
            $classFile = $this->_getClassFile($class);
        }
        require $classFile;
    }

    /**
     * Get class files path based on class name
     *
     * @param $class
     * @return string
     */
    protected function _getClassFile($class)
    {
        return str_replace(array('_', self::NS_SEPARATOR), DIRECTORY_SEPARATOR, $class) . '.php';
    }

    /**
     * Add additional include path
     *
     * @param string|array $path specific path(s) started from system root folder
     * @return Magento_Autoload
     */
    public function addIncludePath($path)
    {
        if (!is_array($path)) {
            $path = array($path);
        }
        $path[] = get_include_path();
        set_include_path(implode(PATH_SEPARATOR, $path));
        return $this;
    }

    /**
     * Add classes files declaration to the map. New map will override existing values if such was defined before.
     *
     * @param array|string $map
     * @param string $basePath
     * @return Magento_Autoload
     */
    public function addFilesMap($map)
    {
        if (is_string($map)) {
            if (is_file($map) && is_readable($map)) {
                $map = include $map;
            } else {
                throw new Exception($map . ' file does not exist.');
            }
        }
        if (is_array($map)) {
            $this->_filesMap = array_merge($this->_filesMap, $map);
        } else {
            throw new Exception('$map parameter should be an array or path map file.');
        }
        return $this;
    }
}

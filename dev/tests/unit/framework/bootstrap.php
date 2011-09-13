<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* Initialize DEV constants */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../../config.php';
date_default_timezone_set('America/Los_Angeles');

if (file_exists('config.php')) {
    require_once 'config.php';
} else {
    require_once 'config.php.dist';
}

/**
 * Bootstrap class for Magento tests
 */
class Magento_Tests_Bootstrap
{
    protected $_includePaths;

    /**
     * Load class file by class name
     *
     * @param string $class
     * @return bool
     */
    public function loadClass($class)
    {
        $filename = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
        /**
         * Validate file exists. Validation is necessary for involving additional autoloaders
         */
        foreach ($this->_includePaths as $path) {
            if (file_exists($path . DIRECTORY_SEPARATOR . $filename)) {
                return include($filename);
            }
        }
        return false;
    }

    /**
     * Initialize testing environment
     *
     * @throws Magento_Exception
     */
    public function __construct()
    {
        /* Setup autoload */
        $this->_includePaths = array(
            dirname(__DIR__) . DIRECTORY_SEPARATOR . 'testsuite',
            DEV_APP . DIRECTORY_SEPARATOR . 'code' . DIRECTORY_SEPARATOR . 'local',
            DEV_APP . DIRECTORY_SEPARATOR . 'code' . DIRECTORY_SEPARATOR . 'community',
            DEV_APP . DIRECTORY_SEPARATOR . 'code' . DIRECTORY_SEPARATOR . 'core',
            DEV_LIB,
        );
        $this->_includePaths = array_merge($this->_includePaths, explode(PATH_SEPARATOR, get_include_path()));
        set_include_path(implode(PATH_SEPARATOR, $this->_includePaths));
        spl_autoload_register(array($this, 'loadClass'));
        /* Check whether temporary directory is writable */
        if (!is_writable(TESTS_TEMP_DIR)) {
            throw new Magento_Exception(
                sprintf('Tests temporary directory "%s" must exist and be writable.', TESTS_TEMP_DIR)
            );
        }
    }

    /**
     * Free resources allocated by tests
     */
    public function __destruct()
    {
        /* Clean up temporary directory */
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(TESTS_TEMP_DIR),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            /* skip subversion directories and temp directory itself */
            if (strpos($file->getRealPath(), '.svn') !== false || $file->getRealPath() == TESTS_TEMP_DIR) {
                continue;
            }
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
    }
}

/* Don't assign bootstrap instance to global variable */
new Magento_Tests_Bootstrap();

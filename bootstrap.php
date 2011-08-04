<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* Initialize DEV constants */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../config.php';
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
     * @throws Varien_Exception
     */
    public function __construct()
    {
        /* Setup autoload */
        $this->_includePaths = array(
            dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'tests',
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
            throw new Varien_Exception(
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
            if (strpos($file->getRealPath(), '.svn') !== false 
                    || $file->getRealPath() == TESTS_TEMP_DIR
                    || $file->getFileName() == '.'
                    || $file->getFileName() == '..'
            ) {
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

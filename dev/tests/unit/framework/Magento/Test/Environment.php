<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Environment management class
 */
class Magento_Test_Environment
{
    /**
     * @var Magento_Test_Environment
     */
    private static $_instance;

    /**
     * Temporary directory
     *
     * @var string
     */
    protected $_tmpDir;

    /**
     * Set self instance for static access
     *
     * @param Magento_Test_Environment $instance
     */
    public static function setInstance(Magento_Test_Environment $instance)
    {
        self::$_instance = $instance;
    }

    /**
     * Self instance getter
     *
     * @return Magento_Test_Environment
     * @throws Magento_Exception
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            throw new Magento_Exception('Environment instance is not defined yet.');
        }
        return self::$_instance;
    }

    /**
     * Initialize instance
     *
     * @param string $tmpDir
     * @throws Magento_Exception
     */
    public function __construct($tmpDir)
    {
        $this->_tmpDir = $tmpDir;
        if (!is_writable($this->_tmpDir)) {
            throw new Magento_Exception($this->_tmpDir . ' must be writable.');
        }
    }

    /**
     * Return path to framework's temporary directory
     *
     * @return string
     */
    public function getTmpDir()
    {
        return $this->_tmpDir;
    }

    /**
     * Clean tmp directory
     *
     * @return Magento_Test_Environment
     */
    public function cleanTmpDir()
    {
        return $this->cleanDir($this->_tmpDir);
    }

    /**
     * Clean directory
     *
     * @param string $dir
     * @return Magento_Test_Environment
     */
    public function cleanDir($dir)
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            if (strpos($file->getFilename(), '.') === 0) {
                continue;
            }
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        return $this;
    }

    /**
     * Clean all files in temp dir
     *
     * @return Magento_Test_Environment
     */
    public function cleanTmpDirOnShutdown()
    {
        register_shutdown_function(array($this, 'cleanTmpDir'));
    }
}

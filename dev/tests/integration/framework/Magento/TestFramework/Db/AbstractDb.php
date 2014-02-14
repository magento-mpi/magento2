<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract database handler for integration tests
 */
namespace Magento\TestFramework\Db;

abstract class AbstractDb
{
    /**
     * DB host name
     *
     * @var string
     */
    protected $_host = '';

    /**
     * DB credentials -- user name
     *
     * @var string
     */
    protected $_user = '';

    /**
     * DB credentials -- password
     *
     * @var string
     */
    protected $_password = '';

    /**
     * DB name
     *
     * @var string
     */
    protected $_schema = '';

    /**
     * Path to a temporary directory in the file system
     *
     * @var string
     */
    protected $_varPath = '';

    /**
     * @var \Magento\Shell
     */
    protected $_shell;

    /**
     * Set initial essential parameters
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $schema
     * @param string $varPath
     * @param \Magento\Shell $shell
     * @throws \Magento\Exception
     */
    public function __construct($host, $user, $password, $schema, $varPath, \Magento\Shell $shell)
    {
        if (!is_dir($varPath) || !is_writable($varPath)) {
            throw new \Magento\Exception("The specified '$varPath' is not a directory or not writable.");
        }
        $this->_host = $host;
        $this->_user = $user;
        $this->_password = $password;
        $this->_schema = $schema;
        $this->_varPath = $varPath;
        $this->_shell = $shell;
    }

    /**
     * Remove all DB objects
     */
    abstract public function cleanup();

    /**
     * Get filename for setup db dump
     *
     * @return string
     */
    abstract protected function getSetupDbDumpFilename();

    /**
     * Is dump esxists
     *
     * @return bool
     */
    abstract public function isDbDumpExists();

    /**
     * Store setup db dump
     */
    abstract public function storeDbDump();

    /**
     * Restore db from setup db dump
     */
    abstract public function restoreFromDbDump();

    /**
     * Create file with sql script content.
     * Utility method that is used in children classes
     *
     * @param string $file
     * @param string $content
     * @return int
     */
    protected function _createScript($file, $content)
    {
        return file_put_contents($file, $content);
    }
}

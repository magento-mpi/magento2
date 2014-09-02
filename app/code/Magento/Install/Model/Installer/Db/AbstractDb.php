<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract resource data model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Install\Model\Installer\Db;

abstract class AbstractDb
{
    /**
     * Resource connection adapter factory
     *
     * @var \Magento\Framework\Model\Resource\Type\Db\Pdo\MysqlFactory
     */
    protected $_adapterFactory;

    /**
     * List of necessary extensions for DBs
     *
     * @var array
     */
    protected $_dbExtensions;

    /**
     *  Adapter instance
     *
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     *  Connection configuration
     *
     * @var array
     */
    protected $_connectionData;

    /**
     *  Connection configuration
     *
     * @var array
     */
    protected $_configData;

    /**
     * Configuration arguments
     *
     * @var \Magento\Framework\App\Arguments
     */
    protected $_arguments;

    /**
     * @param \Magento\Framework\Model\Resource\Type\Db\Pdo\MysqlFactory $adapterFactory
     * @param \Magento\Framework\App\Arguments $arguments
     * @param array $dbExtensions
     */
    public function __construct(
        \Magento\Framework\Model\Resource\Type\Db\Pdo\MysqlFactory $adapterFactory,
        \Magento\Framework\App\Arguments $arguments,
        array $dbExtensions = array()
    ) {
        $this->_adapterFactory = $adapterFactory;
        $this->_dbExtensions = $dbExtensions;
        $this->_arguments = $arguments;
    }

    /**
     * Return the name of DB model from config
     *
     * @return string
     */
    public function getModel()
    {
        return $this->_configData['db_model'];
    }

    /**
     * Return the DB type from config
     *
     * @return string
     */
    public function getType()
    {
        return $this->_configData['db_type'];
    }

    /**
     * Set configuration data
     *
     * @param array $config the connection configuration
     * @return void
     */
    public function setConfig($config)
    {
        $this->_configData = $config;
    }

    /**
     * Retrieve connection data from config
     *
     * @return array
     */
    public function getConnectionData()
    {
        if (!$this->_connectionData) {
            if ($this->_configData) {
                $connectionData = array(
                    'host' => $this->_configData['db_host'],
                    'username' => $this->_configData['db_user'],
                    'password' => $this->_configData['db_pass'],
                    'dbName' => $this->_configData['db_name'],
                    'pdoType' => $this->getPdoType()
                );
            } else {
                $default = $this->_arguments->getConnection('default');
                $connectionData = array(
                    'host' => $default['host'],
                    'username' => $default['username'],
                    'password' => $default['password'],
                    'dbName' => $default['dbName'],
                    'pdoType' => $this->getPdoType()
                );
            }
            $this->_connectionData = $connectionData;
        }
        return $this->_connectionData;
    }

    /**
     * Check InnoDB support
     *
     * @return bool
     */
    public function supportEngine()
    {
        return true;
    }

    /**
     * Create new connection with custom config
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected function _getConnection()
    {
        if (!isset($this->_connection)) {
            $connection = $this->_adapterFactory->create($this->getConnectionData())->getConnection();
            $this->_connection = $connection;
        }
        return $this->_connection;
    }

    /**
     * Return pdo type
     *
     * @return null
     */
    public function getPdoType()
    {
        return null;
    }

    /**
     * Retrieve required PHP extension list for database
     *
     * @return array
     */
    public function getRequiredExtensions()
    {
        return isset($this->_dbExtensions[$this->getModel()]) ? $this->_dbExtensions[$this->getModel()] : array();
    }

    /**
     * Clean up database
     *
     * @return void
     */
    abstract public function cleanUpDatabase();
}

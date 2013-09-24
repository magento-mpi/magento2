<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Resource_Type_Db_Pdo_Mysql extends Magento_Core_Model_Resource_Type_Db
    implements Magento_Core_Model_Resource_ConnectionAdapterInterface
{
    /**
     * Dirs instance
     *
     * @var Magento_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * @var array
     */
    protected $_connectionConfig;

    /**
     * @var string
     */
    protected $_initStatements;

    /**
     * @var boolean
     */
    protected $_isActive;

    /**
     * @param Magento_Core_Model_Dir $dirs
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $dbName
     * @param array $profiler
     * @param string $initStatements
     * @param string $type
     * @param bool $isActive
     */
    public function __construct(
        Magento_Core_Model_Dir $dirs,
        $host,
        $username,
        $password,
        $dbName,
        array $profiler,
        $initStatements = 'SET NAMES utf8',
        $type = 'pdo_mysql',
        $isActive = true
    ) {
        $this->_dirs = $dirs;
        $this->_connectionConfig = array(
            'host' => $host,
            'username' => $username,
            'password' => $password,
            'dbname' => $dbName,
            'type' => $type,
            'profiler' => !empty($profiler) && $profiler !== 'false'
        );

        $this->_host = $host;
        $this->_type = $type;
        $this->_initStatements = $initStatements;
        $this->_isActive = $isActive;
        parent::__construct();
    }

    /**
     * Get connection
     *
     * @return Magento_DB_Adapter_Interface|null
     */
    public function getConnection()
    {
        if (!$this->_isActive) {
            return null;
        }

        $connection = $this->_getDbAdapterInstance();
        if (!empty($this->_initStatements) && $connection) {
            $connection->query($this->_initStatements);
        }

        $profiler = $connection->getProfiler();
        if ($profiler instanceof Magento_DB_Profiler) {
            $profiler->setType($this->_type);
            $profiler->setHost($this->_host);
        }

        return $connection;
    }

    /**
     * Create and return DB adapter object instance
     *
     * @return Magento_DB_Adapter_Pdo_Mysql
     */
    protected function _getDbAdapterInstance()
    {
        $className = $this->_getDbAdapterClassName();
        $adapter = new $className($this->_dirs, $this->_connectionConfig);
        return $adapter;
    }

    /**
     * Retrieve DB adapter class name
     *
     * @return string
     */
    protected function _getDbAdapterClassName()
    {
        return 'Magento_DB_Adapter_Pdo_Mysql';
    }
}

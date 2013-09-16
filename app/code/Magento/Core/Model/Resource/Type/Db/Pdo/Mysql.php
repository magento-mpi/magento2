<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Resource_Type_Db_Pdo_Mysql extends Magento_Core_Model_Resource_Type_Db
{
    /**
     * Dirs instance
     *
     * @var Magento_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * @param Magento_Core_Model_Dir $dirs
     */
    public function __construct(
        Magento_Core_Model_Dir $dirs
    ) {
        $this->_dirs = $dirs;
        parent::__construct();
    }

    /**
     * Get connection
     *
     * @param array $config Connection config
     * @return Magento_DB_Adapter_Pdo_Mysql
     */
    public function getConnection($config)
    {
        $configArr = (array)$config;
        $configArr['profiler'] = !empty($configArr['profiler']) && $configArr['profiler']!=='false';

        $conn = $this->_getDbAdapterInstance($configArr);

        if (!empty($configArr['initStatements']) && $conn) {
            $conn->query($configArr['initStatements']);
        }

        return $conn;
    }

    /**
     * Create and return DB adapter object instance
     *
     * @param array $configArr Connection config
     * @return Magento_DB_Adapter_Pdo_Mysql
     */
    protected function _getDbAdapterInstance($configArr)
    {
        $className = $this->_getDbAdapterClassName();
        $adapter = new $className($this->_dirs, $configArr);
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

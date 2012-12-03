<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Resource_Type_Db_Pdo_Mysql extends Mage_Core_Model_Resource_Type_Db
{
    /**
     * Get connection
     *
     * @param array $config Connection config
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function getConnection($config)
    {
        $conn = $this->_getDbAdapterInstance($config);

        $profiler = $conn->getProfiler();
        if ($profiler instanceof Varien_Db_Profiler) {
            /** @var Varien_Db_Profiler $profiler */
            $host = !empty($config['host']) ? $config['host'] : '';
            $profiler->setHost($host);
            $profiler->setType('pdo_mysql');
        }

        if (!empty($config['initStatements']) && $conn) {
            $conn->query($config['initStatements']);
        }

        return $conn;
    }

    /**
     * Create and return DB adapter object instance
     *
     * @param array $configArr Connection config
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    protected function _getDbAdapterInstance($configArr)
    {
        $className = $this->_getDbAdapterClassName();
        $adapter = new $className($configArr);
        return $adapter;
    }

    /**
     * Retrieve DB adapter class name
     *
     * @return string
     */
    protected function _getDbAdapterClassName()
    {
        return 'Varien_Db_Adapter_Pdo_Mysql';
    }

}

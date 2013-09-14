<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Core\Model\Resource\Type\Db\Pdo;

class Mysql extends \Magento\Core\Model\Resource\Type\Db
{

    /**
     * Get connection
     *
     * @param array $config Connection config
     * @return \Magento\DB\Adapter\Pdo\Mysql
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
     * @return \Magento\DB\Adapter\Pdo\Mysql
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
        return 'Magento\DB\Adapter\Pdo\Mysql';
    }

}

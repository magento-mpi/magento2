<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Local database resource type
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Db_Type_Local extends Mage_Core_Model_Resource_Type_Db
{
    /**
     * Get stub adapter
     *
     * @param array $config Connection config
     * @return Mage_PHPUnit_Db_Adapter
     */
    public function getConnection($config)
    {
        $configArr = (array)$config;
        $configArr['profiler'] = false;

        return $this->_getDbAdapterInstance($configArr);
    }

    /**
     * Create and return stub adapter object instance
     *
     * @param array $configArr Connection config
     * @return Mage_PHPUnit_Db_Adapter
     */
    protected function _getDbAdapterInstance($configArr)
    {
        $className = $this->_getDbAdapterClassName();
        $adapter = new $className($configArr);
        return $adapter;
    }

    /**
     * Retrieve stub adapter class name
     *
     * @return string
     */
    protected function _getDbAdapterClassName()
    {
        return 'Mage_PHPUnit_Db_Adapter';
    }

}

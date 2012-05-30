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
 * Stub class for Mage_Core_Model_Resource.
 * Needed to load real modules configs.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Stub_Mage_Core_Model_Resource extends Mage_Core_Model_Resource
{
    /**
     * Sets connection object.
     * It is needed to mock connection adapter object.
     *
     * @param string $connectionName
     * @param Zend_Db_Adapter_Abstract $connectionObject
     * @return Mage_PHPUnit_Stub_Mage_Core_Model_Resource_Config
     */
    public function setConnection($connectionName, $connectionObject)
    {
        $this->_connections[$connectionName] = $connectionObject;
        return $this;
    }
}

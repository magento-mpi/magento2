<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Helper class for database connections (adapters).
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Helper_Connection extends Mage_PHPUnit_Helper_Abstract
{
    /**
     * Name of the pool with real connection classes
     *
     * @var string
     */
    protected $_connectionClassesPool = Mage_PHPUnit_StaticDataPoolContainer::POOL_CONNECTION_CLASSES;

    /**
     * Sets connection object.
     * Can be needed for setting mock object for DB adapter.
     *
     * @param string $connectionName
     * @param Zend_Db_Adapter_Abstract $connectionObject
     */
    public function setConnection($connectionName, $connectionObject)
    {
        //stub for resource is used for setting connection object.
        //@see Mage_PHPUnit_Stub_Mage_Core_Model_Resource
        Mage::getSingleton('core/resource')->setConnection($connectionName, $connectionObject);
    }

    /**
     * Gets curent connection config.
     *
     * @param string $connectionName
     * @return array
     */
    public function getConnectionConfig($connectionName)
    {
        return Mage::getSingleton('core/resource')->getConnection($connectionName)->getConfig();
    }

    /**
     * Sets connection object.
     * Can be needed for setting mock object for DB adapter.
     *
     * @param string $connectionName
     * @return string
     */
    public function getConnectionClassName($connectionName)
    {
        $className = $this->_getConnectionClassesPool()->getData($connectionName);
        if (!$className) {
            //stub for resource config is used for setting connection object.
            //@see Mage_PHPUnit_Stub_Mage_Core_Model_Resource_Config
            $connection = Mage::getSingleton('core/resource')->getConnection($connectionName);
            $className = get_class($connection);
            $this->_getConnectionClassesPool()->setData($connectionName, $className);
        }
        return $className;
    }

    /**
     * Returns pool of real resource model names
     *
     * @return Mage_PHPUnit_StaticDataPool_Simple
     */
    protected function _getConnectionClassesPool()
    {
        return $this->_getStaticDataObject($this->_connectionClassesPool);
    }

    /**
     * Returns default read resource key from Magento
     *
     * @return string
     */
    public function getDefaultReadResource()
    {
        return Mage_Core_Model_Resource::DEFAULT_READ_RESOURCE;
    }

    /**
     * Returns default write resource key from Magento
     *
     * @return string
     */
    public function getDefaultWriteResource()
    {
        return Mage_Core_Model_Resource::DEFAULT_WRITE_RESOURCE;
    }
}

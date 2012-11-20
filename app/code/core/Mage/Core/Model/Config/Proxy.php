<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Proxy for config class
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Config_Proxy extends Mage_Core_Model_Config
{
    /**
     * Name of real class
     */
    const REAL_CLASS = 'Mage_Core_Model_Config';

    /**
     * Object manager instance
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Constructor
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Retrieve config object
     *
     * @return Mage_Core_Model_Config
     */
    protected function _getConfig()
    {
        return $this->_objectManager->get(self::REAL_CLASS);
    }

    /**
     * Returns node found by the $path and scope info
     *
     * @param  string $path
     * @param  string $scope
     * @param  string|int $scopeCode
     * @return Mage_Core_Model_Config_Element
     */
    public function getNode($path=null, $scope='', $scopeCode=null)
    {
        return $this->_getConfig()->getNode($path, $scope, $scopeCode);
    }

    /**
     * Get module directory by directory type
     *
     * @param  string $type
     * @param  string $moduleName
     * @return string
     */
    public function getModuleDir($type, $moduleName)
    {
        return $this->_getConfig()->getModuleDir($type, $moduleName);
    }
}
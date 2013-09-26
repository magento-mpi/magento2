<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Resource helper pool
 */
class Magento_Core_Model_Resource_HelperPool
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Core_Model_Config_Resource
     */
    protected $_configResource;

    /**
     * @var array
     */
    protected $_resourceHelpers = array();

    /**
     * @param Magento_Core_Model_Config_Resource $configResource
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        Magento_Core_Model_Config_Resource $configResource,
        Magento_ObjectManager $objectManager
    ) {
        $this->_configResource = $configResource;
        $this->_objectManager = $objectManager;
    }

    /**
     * Get resource helper singleton
     *
     * @param string $moduleName
     * @throws InvalidArgumentException
     * @return Magento_Core_Model_Resource_Helper_Abstract
     */
    public function get($moduleName)
    {
        $connectionModel = $this->_configResource->getResourceConnectionModel('core');

        $helperClassName = $moduleName . '_Model_Resource_Helper_' . ucfirst($connectionModel);
        $connection = strtolower($moduleName);
        if (substr($moduleName, 0, 8) == 'Magento_') {
            $connection = substr($connection, 8);
        }

        if (!isset($this->_resourceHelpers[$connection])) {
            $helper = $this->_objectManager->create($helperClassName, array('modulePrefix' => $connection));
            if (false === ($helper instanceof Magento_Core_Model_Resource_Helper_Abstract)) {
                throw new InvalidArgumentException(
                    $helperClassName . ' doesn\'t extend Magento_Core_Model_Resource_Helper_Abstract'
                );
            }
            $this->_resourceHelpers[$connection] = $helper;
        }

        return $this->_resourceHelpers[$connection];
    }
}

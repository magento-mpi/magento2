<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Config backend model for "Custom Admin Path" option
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Model_Config_Backend_Admin_Custompath extends Magento_Core_Model_Config_Value
{
    /**
     * Backend data
     *
     * @var Magento_Backend_Helper_Data
     */
    protected $_backendData = null;

    /**
     * @param Magento_Backend_Helper_Data $backendData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Helper_Data $backendData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_backendData = $backendData;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Check whether redirect should be set
     *
     * @return Magento_Backend_Model_Config_Backend_Admin_Custom
     */
    protected function _beforeSave()
    {
        if ($this->getOldValue() != $this->getValue()) {
            $this->_backendData->clearAreaFrontName();
            $this->_coreRegistry->register('custom_admin_path_redirect', true, true);
        }
        return $this;
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cleanup blocks HTML cache
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_WebsiteRestriction_Model_System_Config_Backend_Active extends Magento_Core_Model_Config_Value
{
    /**
     * @var Magento_Core_Model_App
     */
    protected $_app;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_App $app
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_App $app,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_app = $app;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'magento_websiterestriction_config_active';

    /**
     * Cleanup blocks HTML cache if value has been changed
     *
     * @return Magento_WebsiteRestriction_Model_System_Config_Backend_Active
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            $this->_app->cleanCache(array(Magento_Core_Model_Store::CACHE_TAG, Magento_Cms_Model_Block::CACHE_TAG));
        }
        return parent::_afterSave();
    }
}

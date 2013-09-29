<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend model for shipping table rates CSV importing
 *
 * @category   Magento
 * @package    Magento_Shipping
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Shipping_Model_Config_Backend_Tablerate extends Magento_Core_Model_Config_Value
{
    /**
     * @var Magento_Shipping_Model_Resource_Carrier_TablerateFactory
     */
    protected $_tablerateFactory;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Config $config
     * @param Magento_Shipping_Model_Resource_Carrier_TablerateFactory $tablerateFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Config $config,
        Magento_Shipping_Model_Resource_Carrier_TablerateFactory $tablerateFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_tablerateFactory = $tablerateFactory;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    public function _afterSave()
    {
        $this->_tablerateFactory->create()->uploadAndImport($this);
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Gift Message resource setup
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftMessage_Model_Resource_Setup extends Magento_Sales_Model_Resource_Setup
{
    /**
     * @var Magento_Catalog_Model_Resource_SetupFactory
     */
    protected $_catalogSetupFactory;

    /**
     * @param Magento_Catalog_Model_Resource_SetupFactory $catalogSetupFactory
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Core_Model_Resource_Setup_Context $context
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        Magento_Catalog_Model_Resource_SetupFactory $catalogSetupFactory,
        Magento_Core_Model_Config $config,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Model_CacheInterface $cache,
        Magento_Core_Model_Resource_Setup_Context $context,
        $resourceName,
        $moduleName = 'Magento_GiftMessage',
        $connectionName = ''
    ) {
        $this->_catalogSetupFactory = $catalogSetupFactory;
        parent::__construct($config, $coreData, $cache, $context, $resourceName, $moduleName, $connectionName);
    }


    /**
     * Create Catalog Setup Factory for GiftMessage
     *
     * @param array $data
     * @return Magento_Catalog_Model_Resource_Setup
     */
    public function createGiftMessageSetup(array $data = array())
    {
        return $this->_catalogSetupFactory->create($data);
    }
}

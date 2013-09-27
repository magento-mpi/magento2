<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift wrapping resource setup
 */
class Magento_GiftWrapping_Model_Resource_Setup extends Magento_Sales_Model_Resource_Setup
{
    /**
     * @var Magento_Catalog_Model_Product_TypeFactory
     */
    protected $_productTypeFactory;

    /**
     * @var Magento_Catalog_Model_Resource_SetupFactory
     */
    protected $_catalogSetupFactory;

    /**
     * @param Magento_Core_Model_Resource_Setup_Context $context
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Catalog_Model_Product_TypeFactory $productTypeFactory
     * @param Magento_Catalog_Model_Resource_SetupFactory $catalogSetupFactory
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        Magento_Core_Model_Resource_Setup_Context $context,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_CacheInterface $cache,
        Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Catalog_Model_Product_TypeFactory $productTypeFactory,
        Magento_Catalog_Model_Resource_SetupFactory $catalogSetupFactory,
        $resourceName,
        $moduleName = 'Magento_GiftWrapping',
        $connectionName = ''
    ) {
        $this->_productTypeFactory = $productTypeFactory;
        $this->_catalogSetupFactory = $catalogSetupFactory;
        parent::__construct($context, $config, $cache, $migrationFactory, $coreData,
            $resourceName, $moduleName, $connectionName
        );
    }

    /**
     * @return Magento_Catalog_Model_Product_Type
     */
    public function getProductType()
    {
        return $this->_productTypeFactory->create();
    }

    /**
     * @return Magento_Catalog_Model_Resource_Setup
     */
    public function getCatalogSetup()
    {
        return $this->_catalogSetupFactory->create(array('resourceName' => 'catalog_setup'));
    }
}

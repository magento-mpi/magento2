<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog product group price backend attribute model
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_Catalog_Model_Product_Attribute_Backend_Groupprice
    extends Magento_Catalog_Model_Product_Attribute_Backend_Groupprice_Abstract
{
    /**
     * Catalog product attribute backend groupprice
     *
     * @var Magento_Catalog_Model_Resource_Product_Attribute_Backend_Groupprice
     */
    protected $_productAttributeBackendGroupprice;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Directory_Model_CurrencyFactory $currencyFactory
     * @param Magento_Catalog_Model_Resource_Product_Attribute_Backend_Groupprice $productAttributeBackendGroupprice
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Catalog_Model_Product_Type $catalogProductType
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Model_Config $config
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Directory_Model_CurrencyFactory $currencyFactory,
        Magento_Catalog_Model_Resource_Product_Attribute_Backend_Groupprice $productAttributeBackendGroupprice,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Catalog_Model_Product_Type $catalogProductType,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Model_Config $config
    ) {
        $this->_productAttributeBackendGroupprice = $productAttributeBackendGroupprice;
        parent::__construct($logger, $currencyFactory, $storeManager, $catalogProductType, $catalogData,
            $config);
    }

    /**
     * Retrieve resource instance
     *
     * @return Magento_Catalog_Model_Resource_Product_Attribute_Backend_Groupprice
     */
    protected function _getResource()
    {
        return $this->_productAttributeBackendGroupprice;
    }

    /**
     * Error message when duplicates
     *
     * @return string
     */
    protected function _getDuplicateErrorMessage()
    {
        return __('We found a duplicate website group price customer group.');
    }
}

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
 * Catalog product tier price backend attribute model
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Attribute_Backend_Tierprice
    extends Magento_Catalog_Model_Product_Attribute_Backend_Groupprice_Abstract
{
    /**
     * Catalog product attribute backend tierprice
     *
     * @var Magento_Catalog_Model_Resource_Product_Attribute_Backend_Tierprice
     */
    protected $_productAttributeBackendTierprice;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Directory_Model_CurrencyFactory $currencyFactory
     * @param Magento_Catalog_Model_Resource_Product_Attribute_Backend_Tierprice $productAttributeTierprice
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Catalog_Model_Product_Type $catalogProductType
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Model_Config $config
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Directory_Model_CurrencyFactory $currencyFactory,
        Magento_Catalog_Model_Resource_Product_Attribute_Backend_Tierprice $productAttributeTierprice,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Catalog_Model_Product_Type $catalogProductType,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Model_Config $config
    ) {
        $this->_productAttributeBackendTierprice = $productAttributeTierprice;
        parent::__construct($logger, $currencyFactory, $storeManager, $catalogProductType, $catalogData,
            $config);
    }

    /**
     * Retrieve resource instance
     *
     * @return Magento_Catalog_Model_Resource_Product_Attribute_Backend_Tierprice
     */
    protected function _getResource()
    {
        return $this->_productAttributeBackendTierprice;
    }

    /**
     * Retrieve websites rates and base currency codes
     *
     * @deprecated since 1.12.0.0
     * @return array
     */
    public function _getWebsiteRates()
    {
        return $this->_getWebsiteCurrencyRates();
    }

    /**
     * Add price qty to unique fields
     *
     * @param array $objectArray
     * @return array
     */
    protected function _getAdditionalUniqueFields($objectArray)
    {
        $uniqueFields = parent::_getAdditionalUniqueFields($objectArray);
        $uniqueFields['qty'] = $objectArray['price_qty'] * 1;
        return $uniqueFields;
    }

    /**
     * Error message when duplicates
     *
     * @return string
     */
    protected function _getDuplicateErrorMessage()
    {
        return __('We found a duplicate website, tier price, customer group and quantity.');
    }

    /**
     * Whether tier price value fixed or percent of original price
     *
     * @param Magento_Catalog_Model_Product_Type_Price $priceObject
     * @return bool
     */
    protected function _isPriceFixed($priceObject)
    {
        return $priceObject->isTierPriceFixed();
    }
}

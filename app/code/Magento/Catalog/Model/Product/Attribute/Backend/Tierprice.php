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
namespace Magento\Catalog\Model\Product\Attribute\Backend;

class Tierprice
    extends \Magento\Catalog\Model\Product\Attribute\Backend\Groupprice\AbstractGroupprice
{
    /**
     * Catalog product attribute backend tierprice
     *
     * @var \Magento\Catalog\Model\Resource\Product\Attribute\Backend\Tierprice
     */
    protected $_productAttributeBackendTierprice;

    /**
     * Construct
     *
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Catalog\Model\Resource\Product\Attribute\Backend\Tierprice $productAttributeTierprice
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Product\Type $catalogProductType
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Model\Config $config
     */
    public function __construct(
        \Magento\Core\Model\Logger $logger,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Catalog\Model\Resource\Product\Attribute\Backend\Tierprice $productAttributeTierprice,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product\Type $catalogProductType,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Model\Config $config
    ) {
        $this->_productAttributeBackendTierprice = $productAttributeTierprice;
        parent::__construct($logger, $currencyFactory, $storeManager, $catalogProductType, $catalogData,
            $config);
    }

    /**
     * Retrieve resource instance
     *
     * @return \Magento\Catalog\Model\Resource\Product\Attribute\Backend\Tierprice
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
     * @param \Magento\Catalog\Model\Product\Type\Price $priceObject
     * @return bool
     */
    protected function _isPriceFixed($priceObject)
    {
        return $priceObject->isTierPriceFixed();
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Price attribute model
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Model_Attribute_Price extends Magento_GoogleShopping_Model_Attribute_Default
{
    /**
     * @var Magento_Tax_Helper_Data|null
     */
    protected $_taxData = null;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * Config
     *
     * @var Magento_GoogleShopping_Model_Config
     */
    protected $_config;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_GoogleShopping_Helper_Data $gsData
     * @param Magento_GoogleShopping_Helper_Product $gsProduct
     * @param Magento_GoogleShopping_Helper_Price $gsPrice
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_GoogleShopping_Model_Config $config
     * @param Magento_GoogleShopping_Model_Resource_Attribute $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Tax_Helper_Data $taxData,
        Magento_GoogleShopping_Helper_Data $gsData,
        Magento_GoogleShopping_Helper_Product $gsProduct,
        Magento_GoogleShopping_Helper_Price $gsPrice,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_GoogleShopping_Model_Config $config,
        Magento_GoogleShopping_Model_Resource_Attribute $resource,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_config = $config;
        $this->_taxData = $taxData;
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($productFactory, $gsData, $gsProduct, $gsPrice, $context, $registry, $resource,
            $resourceCollection, $data);
    }

    /**
     * Set current attribute to entry (for specified product)
     *
     * @param Magento_Catalog_Model_Product $product
     * @param Magento_Gdata_Gshopping_Entry $entry
     * @return Magento_Gdata_Gshopping_Entry
     */
    public function convertAttribute($product, $entry)
    {
        $product->setWebsiteId($this->_storeManager->getStore($product->getStoreId())->getWebsiteId());
        $product->setCustomerGroupId(
            $this->_coreStoreConfig->getConfig(Magento_Customer_Model_Group::XML_PATH_DEFAULT_ID, $product->getStoreId())
        );

        $store = $this->_storeManager->getStore($product->getStoreId());
        $targetCountry = $this->_config->getTargetCountry($product->getStoreId());
        $isSalePriceAllowed = ($targetCountry == 'US');

        // get tax settings
        $taxHelp = $this->_taxData;
        $priceDisplayType = $taxHelp->getPriceDisplayType($product->getStoreId());
        $inclTax = ($priceDisplayType == Magento_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX);

        // calculate sale_price attribute value
        $salePriceAttribute = $this->getGroupAttributeSalePrice();
        $salePriceMapValue = null;
        $finalPrice = null;
        if (!is_null($salePriceAttribute)) {
            $salePriceMapValue = $salePriceAttribute->getProductAttributeValue($product);
        }
        if (!is_null($salePriceMapValue) && floatval($salePriceMapValue) > .0001) {
            $finalPrice = $salePriceMapValue;
        } else if ($isSalePriceAllowed) {
            $finalPrice = $this->_gsPrice->getCatalogPrice($product, $store, $inclTax);
        }
        if ($product->getTypeId() != Magento_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            $finalPrice = $taxHelp->getPrice($product, $finalPrice, $inclTax, null, null, null, $product->getStoreId());
        }

        // calculate price attribute value
        $priceMapValue = $this->getProductAttributeValue($product);
        $price = null;
        if (!is_null($priceMapValue) && floatval($priceMapValue) > .0001) {
            $price = $priceMapValue;
        } else if ($isSalePriceAllowed) {
            $price = $this->_gsPrice->getCatalogRegularPrice($product, $store);
        } else {
            $inclTax = ($priceDisplayType != Magento_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX);
            $price = $this->_gsPrice->getCatalogPrice($product, $store, $inclTax);
        }
        if ($product->getTypeId() != Magento_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            $price = $taxHelp->getPrice($product, $price, $inclTax, null, null, null, $product->getStoreId());
        }

        if ($isSalePriceAllowed) {
            // set sale_price and effective dates for it
            if ($price && ($price - $finalPrice) > .0001) {
                $this->_setAttributePrice($entry, $product, $targetCountry, $price);
                $this->_setAttributePrice($entry, $product, $targetCountry, $finalPrice, 'sale_price');

                $effectiveDate = $this->getGroupAttributeSalePriceEffectiveDate();
                if (!is_null($effectiveDate)) {
                    $effectiveDate->setGroupAttributeSalePriceEffectiveDateFrom(
                            $this->getGroupAttributeSalePriceEffectiveDateFrom()
                        )
                        ->setGroupAttributeSalePriceEffectiveDateTo($this->getGroupAttributeSalePriceEffectiveDateTo())
                        ->convertAttribute($product, $entry);
                }
            } else {
                $this->_setAttributePrice($entry, $product, $targetCountry, $finalPrice);
                $entry->removeContentAttribute('sale_price_effective_date');
                $entry->removeContentAttribute('sale_price');
            }

            // calculate taxes
            $tax = $this->getGroupAttributeTax();
            if (!$inclTax && !is_null($tax)) {
                $tax->convertAttribute($product, $entry);
            }
        } else {
            $this->_setAttributePrice($entry, $product, $targetCountry, $price);
        }

        return $entry;
    }

    /**
     * Custom setter for 'price' attribute
     *
     * @param Magento_Gdata_Gshopping_Entry $entry
     * @param string $attribute Google Content attribute name
     * @param mixed $value Fload price value
     * @param string $type Google Content attribute type
     * @param string $name Google Content attribute name
     * @return Magento_Gdata_Gshopping_Entry
     */
    protected function _setAttributePrice($entry, $product, $targetCountry, $value, $name = 'price')
    {
        $store = $this->_storeManager->getStore($product->getStoreId());
        $price = $store->convertPrice($value);
        return $this->_setAttribute($entry,
            $name,
            self::ATTRIBUTE_TYPE_FLOAT,
            sprintf('%.2f', $store->roundPrice($price)),
            $store->getDefaultCurrencyCode()
        );
    }
}

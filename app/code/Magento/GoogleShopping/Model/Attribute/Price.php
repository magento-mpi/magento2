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
namespace Magento\GoogleShopping\Model\Attribute;

class Price extends \Magento\GoogleShopping\Model\Attribute\DefaultAttribute
{
    /**
     * @var \Magento\Tax\Helper\Data|null
     */
    protected $_taxData = null;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_GoogleShopping_Helper_Data $gsData
     * @param Magento_GoogleShopping_Helper_Product $gsProduct
     * @param Magento_GoogleShopping_Helper_Price $gsPrice
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_GoogleShopping_Model_Resource_Attribute $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Tax\Helper\Data $taxData,
        \Magento\GoogleShopping\Helper\Data $gsData,
        \Magento\GoogleShopping\Helper\Product $gsProduct,
        \Magento\GoogleShopping\Helper\Price $gsPrice,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        \Magento\GoogleShopping\Model\Resource\Attribute $resource,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_taxData = $taxData;
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($gsData, $gsProduct, $gsPrice, $context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Set current attribute to entry (for specified product)
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Gdata\Gshopping\Entry $entry
     * @return \Magento\Gdata\Gshopping\Entry
     */
    public function convertAttribute($product, $entry)
    {
        $product->setWebsiteId(\Mage::app()->getStore($product->getStoreId())->getWebsiteId());
        $product->setCustomerGroupId(
            $this->_coreStoreConfig->getConfig(Magento_Customer_Model_Group::XML_PATH_DEFAULT_ID, $product->getStoreId())
        );

        $store = \Mage::app()->getStore($product->getStoreId());
        $targetCountry = \Mage::getSingleton('Magento\GoogleShopping\Model\Config')->getTargetCountry($product->getStoreId());
        $isSalePriceAllowed = ($targetCountry == 'US');

        // get tax settings
        $taxHelp = $this->_taxData;
        $priceDisplayType = $taxHelp->getPriceDisplayType($product->getStoreId());
        $inclTax = ($priceDisplayType == \Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX);

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
        if ($product->getTypeId() != \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
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
            $inclTax = ($priceDisplayType != \Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX);
            $price = $this->_gsPrice->getCatalogPrice($product, $store, $inclTax);
        }
        if ($product->getTypeId() != \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
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
     * @param \Magento\Gdata\Gshopping\Entry $entry
     * @param string $attribute Google Content attribute name
     * @param mixed $value Fload price value
     * @param string $type Google Content attribute type
     * @param string $name Google Content attribute name
     * @return \Magento\Gdata\Gshopping\Entry
     */
    protected function _setAttributePrice($entry, $product, $targetCountry, $value, $name = 'price')
    {
        $store = \Mage::app()->getStore($product->getStoreId());
        $price = $store->convertPrice($value);
        return $this->_setAttribute($entry,
            $name,
            self::ATTRIBUTE_TYPE_FLOAT,
            sprintf('%.2f', $store->roundPrice($price)),
            $store->getDefaultCurrencyCode()
        );
    }
}

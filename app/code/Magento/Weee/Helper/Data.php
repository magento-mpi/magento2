<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Weee
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * WEEE data helper
 *
 * @category Magento
 * @package  Magento_Weee
 * @author   Magento Core Team <core@magentocommerce.com>
 */
class Magento_Weee_Helper_Data extends Magento_Core_Helper_Abstract
{

    const XML_PATH_FPT_ENABLED       = 'tax/weee/enable';

    protected $_storeDisplayConfig   = array();

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;
    
    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Model_Store_Config $coreStoreConfig
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context);
    }

    /**
     * Get weee amount display type on product view page
     *
     * @param   mixed $store
     * @return  int
     */
    public function getPriceDisplayType($store = null)
    {
        return $this->_coreStoreConfig->getConfig('tax/weee/display', $store);
    }

    /**
     * Get weee amount display type on product list page
     *
     * @param   mixed $store
     * @return  int
     */
    public function getListPriceDisplayType($store = null)
    {
        return $this->_coreStoreConfig->getConfig('tax/weee/display_list', $store);
    }

    /**
     * Get weee amount display type in sales modules
     *
     * @param   mixed $store
     * @return  int
     */
    public function getSalesPriceDisplayType($store = null)
    {
        return $this->_coreStoreConfig->getConfig('tax/weee/display_sales', $store);
    }

    /**
     * Get weee amount display type in email templates
     *
     * @param   mixed $store
     * @return  int
     */
    public function getEmailPriceDisplayType($store = null)
    {
        return $this->_coreStoreConfig->getConfig('tax/weee/display_email', $store);
    }

    /**
     * Check if weee tax amount should be discounted
     *
     * @param   mixed $store
     * @return  bool
     */
    public function isDiscounted($store = null)
    {
        return $this->_coreStoreConfig->getConfigFlag('tax/weee/discount', $store);
    }

    /**
     * Check if weee tax amount should be taxable
     *
     * @param   mixed $store
     * @return  bool
     */
    public function isTaxable($store = null)
    {
        return $this->_coreStoreConfig->getConfigFlag('tax/weee/apply_vat', $store);
    }

    /**
     * Check if weee tax amount should be included to subtotal
     *
     * @param   mixed $store
     * @return  bool
     */
    public function includeInSubtotal($store = null)
    {
        return $this->_coreStoreConfig->getConfigFlag('tax/weee/include_in_subtotal', $store);
    }

    /**
     * Get weee tax amount for product based on shipping and billing addresses, website and tax settings
     *
     * @param   Magento_Catalog_Model_Product $product
     * @param   null|Magento_Customer_Model_Address_Abstract $shipping
     * @param   null|Magento_Customer_Model_Address_Abstract $billing
     * @param   mixed $website
     * @param   bool $calculateTaxes
     * @return  float
     */
    public function getAmount($product, $shipping = null, $billing = null, $website = null, $calculateTaxes = false)
    {
        if ($this->isEnabled()) {
            return Mage::getSingleton('Magento_Weee_Model_Tax')->
                    getWeeeAmount($product, $shipping, $billing, $website, $calculateTaxes);
        }
        return 0;
    }

    /**
     * Returns diaplay type for price accordingly to current zone
     *
     * @param Magento_Catalog_Model_Product $product
     * @param array|null                 $compareTo
     * @param string                     $zone
     * @param Magento_Core_Model_Store      $store
     * @return bool|int
     */
    public function typeOfDisplay($product, $compareTo = null, $zone = null, $store = null)
    {
        if (!$this->isEnabled($store)) {
            return false;
        }
        switch ($zone) {
            case 'product_view':
                $type = $this->getPriceDisplayType($store);
                break;
            case 'product_list':
                $type = $this->getListPriceDisplayType($store);
                break;
            case 'sales':
                $type = $this->getSalesPriceDisplayType($store);
                break;
            case 'email':
                $type = $this->getEmailPriceDisplayType($store);
                break;
            default:
                if ($this->_coreRegistry->registry('current_product')) {
                    $type = $this->getPriceDisplayType($store);
                } else {
                    $type = $this->getListPriceDisplayType($store);
                }
                break;
        }

        if (is_null($compareTo)) {
            return $type;
        } else {
            if (is_array($compareTo)) {
                return in_array($type, $compareTo);
            } else {
                return $type == $compareTo;
            }
        }
    }

    /**
     * Proxy for Magento_Weee_Model_Tax::getProductWeeeAttributes()
     *
     * @param Magento_Catalog_Model_Product $product
     * @param null|false|Magento_Object   $shipping
     * @param null|false|Magento_Object   $billing
     * @param Magento_Core_Model_Website    $website
     * @param bool                       $calculateTaxes
     * @return array
     */
    public function getProductWeeeAttributes($product, $shipping = null, $billing = null,
        $website = null, $calculateTaxes = false)
    {
        return Mage::getSingleton('Magento_Weee_Model_Tax')
                ->getProductWeeeAttributes($product, $shipping, $billing, $website, $calculateTaxes);
    }

    /**
     * Returns applied weee taxes
     *
     * @param Magento_Sales_Model_Quote_Item_Abstract $item
     * @return array
     */
    public function getApplied($item)
    {
        if ($item instanceof Magento_Sales_Model_Quote_Item_Abstract) {
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                $result = array();
                foreach ($item->getChildren() as $child) {
                    $childData = $this->getApplied($child);
                    if (is_array($childData)) {
                        $result = array_merge($result, $childData);
                    }
                }
                return $result;
            }
        }

        /**
         * if order item data is old enough then weee_tax_applied cab be
         * not valid serialized data
         */
        $data = $item->getWeeeTaxApplied();
        if (empty($data)){
            return array();
        }
        return unserialize($item->getWeeeTaxApplied());
    }

    /**
     * Sets applied weee taxes
     *
     * @param Magento_Sales_Model_Quote_Item_Abstract $item
     * @param array                                $value
     * @return Magento_Weee_Helper_Data
     */
    public function setApplied($item, $value)
    {
        $item->setWeeeTaxApplied(serialize($value));
        return $this;
    }

    /**
     * Returns array of weee attributes allowed for display
     *
     * @param Magento_Catalog_Model_Product $product
     * @return array
     */
    public function getProductWeeeAttributesForDisplay($product)
    {
        if ($this->isEnabled()) {
            return $this->getProductWeeeAttributes($product, null, null, null, $this->typeOfDisplay($product, 1));
        }
        return array();
    }

    /**
     * Get Product Weee attributes for price renderer
     *
     * @param Magento_Catalog_Model_Product $product
     * @param null|false|Magento_Object $shipping Shipping Address
     * @param null|false|Magento_Object $billing Billing Address
     * @param null|Magento_Core_Model_Website $website
     * @param mixed $calculateTaxes
     * @return array
     */
    public function getProductWeeeAttributesForRenderer($product, $shipping = null, $billing = null,
        $website = null, $calculateTaxes = false)
    {
        if ($this->isEnabled()) {
            return $this->getProductWeeeAttributes(
                $product,
                $shipping,
                $billing,
                $website,
                $calculateTaxes ? $calculateTaxes : $this->typeOfDisplay($product, 1)
            );
        }
        return array();
    }

    /**
     * Returns amount to display
     *
     * @param Magento_Catalog_Model_Product $product
     * @return int
     */
    public function getAmountForDisplay($product)
    {
        if ($this->isEnabled()) {
            return Mage::getModel('Magento_Weee_Model_Tax')
                    ->getWeeeAmount($product, null, null, null, $this->typeOfDisplay($product, 1));
        }
        return 0;
    }

    /**
     * Returns original amount
     *
     * @param Magento_Catalog_Model_Product $product
     * @return int
     */
    public function getOriginalAmount($product)
    {
        if ($this->isEnabled()) {
            return Mage::getModel('Magento_Weee_Model_Tax')->getWeeeAmount($product, null, null, null, false, true);
        }
        return 0;
    }

    /**
     * Adds HTML containers and formats tier prices accordingly to the currency used
     *
     * @param Magento_Catalog_Model_Product $product
     * @param array                      $tierPrices
     * @return Magento_Weee_Helper_Data
     */
    public function processTierPrices($product, &$tierPrices)
    {
        $weeeAmount = $this->getAmountForDisplay($product);
        $store = Mage::app()->getStore();
        foreach ($tierPrices as $index => &$tier) {
            $html = $store->formatPrice($store->convertPrice(
                Mage::helper('Magento_Tax_Helper_Data')->getPrice($product, $tier['website_price'], true)+$weeeAmount), false);
            $tier['formated_price_incl_weee'] = '<span class="price tier-' . $index . '-incl-tax">' . $html . '</span>';
            $html = $store->formatPrice($store->convertPrice(
                Mage::helper('Magento_Tax_Helper_Data')->getPrice($product, $tier['website_price'])+$weeeAmount), false);
            $tier['formated_price_incl_weee_only'] = '<span class="price tier-' . $index . '">' . $html . '</span>';
            $tier['formated_weee'] = $store->formatPrice($store->convertPrice($weeeAmount));
        }
        return $this;
    }

    /**
     * Check if fixed taxes are used in system
     *
     * @param Magento_Core_Model_Store $store
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return $this->_coreStoreConfig->getConfig(self::XML_PATH_FPT_ENABLED, $store);
    }

    /**
     * Returns all summed WEEE taxes with all local taxes applied
     *
     * @throws Magento_Exception
     * @param array $attributes Array of Magento_Object, result from getProductWeeeAttributes()
     * @return float
     */
    public function getAmountInclTaxes($attributes)
    {
        if (is_array($attributes)) {
            $amount = 0;
            foreach ($attributes as $attribute) {
                /* @var $attribute Magento_Object */
                $amount += $attribute->getAmount() + $attribute->getTaxAmount();
            }
        } else {
            throw new Magento_Exception('$attributes must be an array');
        }

        return (float)$amount;
    }
}

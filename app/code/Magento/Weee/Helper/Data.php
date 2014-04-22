<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Weee
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Weee\Helper;

use Magento\Store\Model\Store;
use Magento\Store\Model\Website;

/**
 * WEEE data helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Enabled config path
     */
    const XML_PATH_FPT_ENABLED = 'tax/weee/enable';

    /**
     * @var array
     */
    protected $_storeDisplayConfig = array();

    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry;

    /**
     * Tax data
     *
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxData;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Weee\Model\Tax
     */
    protected $_weeeTax;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Weee\Model\Tax $weeeTax
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Registry $coreRegistry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Weee\Model\Tax $weeeTax,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Registry $coreRegistry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_storeManager = $storeManager;
        $this->_weeeTax = $weeeTax;
        $this->_coreRegistry = $coreRegistry;
        $this->_taxData = $taxData;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * Get weee amount display type on product view page
     *
     * @param   null|string|bool|int|Store $store
     * @return  int
     */
    public function getPriceDisplayType($store = null)
    {
        return $this->_scopeConfig->getValue(
            'tax/weee/display',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get weee amount display type on product list page
     *
     * @param   null|string|bool|int|Store $store
     * @return  int
     */
    public function getListPriceDisplayType($store = null)
    {
        return $this->_scopeConfig->getValue(
            'tax/weee/display_list',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get weee amount display type in sales modules
     *
     * @param   null|string|bool|int|Store $store
     * @return  int
     */
    public function getSalesPriceDisplayType($store = null)
    {
        return $this->_scopeConfig->getValue(
            'tax/weee/display_sales',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get weee amount display type in email templates
     *
     * @param   null|string|bool|int|Store $store
     * @return  int
     */
    public function getEmailPriceDisplayType($store = null)
    {
        return $this->_scopeConfig->getValue(
            'tax/weee/display_email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Check if weee tax amount should be discounted
     *
     * @param   null|string|bool|int|Store $store
     * @return  bool
     */
    public function isDiscounted($store = null)
    {
        return $this->_scopeConfig->isSetFlag(
            'tax/weee/discount',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Check if weee tax amount should be taxable
     *
     * @param   null|string|bool|int|Store $store
     * @return  bool
     */
    public function isTaxable($store = null)
    {
        return $this->_scopeConfig->isSetFlag(
            'tax/weee/apply_vat',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Check if weee tax amount should be included to subtotal
     *
     * @param   null|string|bool|int|Store $store
     * @return  bool
     */
    public function includeInSubtotal($store = null)
    {
        return $this->_scopeConfig->isSetFlag(
            'tax/weee/include_in_subtotal',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get weee tax amount for product based on website
     *
     * @param   \Magento\Catalog\Model\Product $product
     * @param   mixed $website
     * @return  float
     */
    public function getAmount($product, $website = null)
    {
        if ($this->isEnabled()) {
            return $this->_weeeTax->getWeeeAmount($product, null, null, $website, false);
        }
        return 0;
    }

    /**
     * Returns display type for price accordingly to current zone
     *
     * @param int|int[]|null                 $compareTo
     * @param string                         $zone
     * @param Store                          $store
     * @return bool|int
     */
    public function typeOfDisplay($compareTo = null, $zone = \Magento\Pricing\Render::ZONE_DEFAULT, $store = null)
    {
        if (!$this->isEnabled($store)) {
            return false;
        }
        switch ($zone) {
            case \Magento\Pricing\Render::ZONE_ITEM_VIEW:
                $type = $this->getPriceDisplayType($store);
                break;
            case \Magento\Pricing\Render::ZONE_ITEM_LIST:
                $type = $this->getListPriceDisplayType($store);
                break;
            case \Magento\Pricing\Render::ZONE_SALES:
                $type = $this->getSalesPriceDisplayType($store);
                break;
            case \Magento\Pricing\Render::ZONE_EMAIL:
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
     * Proxy for \Magento\Weee\Model\Tax::getProductWeeeAttributes()
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param null|false|\Magento\Object     $shipping
     * @param null|false|\Magento\Object     $billing
     * @param Website                        $website
     * @param bool                           $calculateTaxes
     * @return \Magento\Object[]
     */
    public function getProductWeeeAttributes(
        $product,
        $shipping = null,
        $billing = null,
        $website = null,
        $calculateTaxes = false
    ) {
        return $this->_weeeTax->getProductWeeeAttributes(
            $product,
            $shipping,
            $billing,
            $website,
            $calculateTaxes
        );
    }

    /**
     * Returns applied weee taxes
     *
     * @param \Magento\Sales\Model\Quote\Item\AbstractItem $item
     * @return array
     */
    public function getApplied($item)
    {
        if ($item instanceof \Magento\Sales\Model\Quote\Item\AbstractItem) {
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
        if (empty($data)) {
            return array();
        }
        return unserialize($item->getWeeeTaxApplied());
    }

    /**
     * Sets applied weee taxes
     *
     * @param \Magento\Sales\Model\Quote\Item\AbstractItem $item
     * @param array $value
     * @return $this
     */
    public function setApplied($item, $value)
    {
        $item->setWeeeTaxApplied(serialize($value));
        return $this;
    }

    /**
     * Returns array of weee attributes allowed for display
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Object[]
     */
    public function getProductWeeeAttributesForDisplay($product)
    {
        if ($this->isEnabled()) {
            return $this->getProductWeeeAttributes($product, null, null, null, $this->typeOfDisplay(1));
        }
        return array();
    }

    /**
     * Get Product Weee attributes for price renderer
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param null|false|\Magento\Object $shipping Shipping Address
     * @param null|false|\Magento\Object $billing Billing Address
     * @param null|Website $website
     * @param bool $calculateTaxes
     * @return \Magento\Object[]
     */
    public function getProductWeeeAttributesForRenderer(
        $product,
        $shipping = null,
        $billing = null,
        $website = null,
        $calculateTaxes = false
    ) {
        if ($this->isEnabled()) {
            return $this->getProductWeeeAttributes(
                $product,
                $shipping,
                $billing,
                $website,
                $calculateTaxes ? $calculateTaxes : $this->typeOfDisplay(1)
            );
        }
        return array();
    }

    /**
     * Returns amount to display
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return int
     */
    public function getAmountForDisplay($product)
    {
        if ($this->isEnabled()) {
            return $this->_weeeTax->getWeeeAmount($product, null, null, null, $this->typeOfDisplay(1));
        }
        return 0;
    }

    /**
     * Returns original amount
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return int
     */
    public function getOriginalAmount($product)
    {
        if ($this->isEnabled()) {
            return $this->_weeeTax->getWeeeAmount($product, null, null, null, false, true);
        }
        return 0;
    }

    /**
     * Adds HTML containers and formats tier prices accordingly to the currency used
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array                          &$tierPrices
     * @return $this
     */
    public function processTierPrices($product, &$tierPrices)
    {
        $weeeAmount = $this->getAmountForDisplay($product);
        $store = $this->_storeManager->getStore();
        foreach ($tierPrices as $index => &$tier) {
            $html = $store->formatPrice(
                $store->convertPrice($this->_taxData->getPrice($product, $tier['website_price'], true) + $weeeAmount),
                false
            );
            $tier['formated_price_incl_weee'] = '<span class="price tier-' . $index . '-incl-tax">' . $html . '</span>';
            $html = $store->formatPrice(
                $store->convertPrice($this->_taxData->getPrice($product, $tier['website_price']) + $weeeAmount),
                false
            );
            $tier['formated_price_incl_weee_only'] = '<span class="price tier-' . $index . '">' . $html . '</span>';
            $tier['formated_weee'] = $store->formatPrice($store->convertPrice($weeeAmount));
        }
        return $this;
    }

    /**
     * Check if fixed taxes are used in system
     *
     * @param Store $store
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_FPT_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Returns all summed WEEE taxes with all local taxes applied
     *
     * @param \Magento\Object[] $attributes Array of \Magento\Object, result from getProductWeeeAttributes()
     * @return float
     * @throws \Magento\Exception
     */
    public function getAmountInclTaxes($attributes)
    {
        if (!is_array($attributes)) {
            throw new \Magento\Exception('$attributes must be an array');
        }

        $amount = 0;
        foreach ($attributes as $attribute) {
            /* @var $attribute \Magento\Object */
            $amount += $attribute->getAmount() + $attribute->getTaxAmount();
        }

        return (float) $amount;
    }
}

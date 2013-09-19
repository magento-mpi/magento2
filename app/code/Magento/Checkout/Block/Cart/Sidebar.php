<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Wishlist sidebar block
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Block_Cart_Sidebar extends Magento_Checkout_Block_Cart_Abstract
{
    const XML_PATH_CHECKOUT_SIDEBAR_COUNT   = 'checkout/sidebar/count';

    /**
     * Tax data
     *
     * @var Magento_Tax_Helper_Data
     */
    protected $_taxData = null;

    /**
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        parent::__construct($catalogData, $coreData, $context, $data);
        $this->_taxData = $taxData;
    }

    /**
     * Retrieve count of display recently added items
     *
     * @return int
     */
    public function getItemCount()
    {
        $count = $this->getData('item_count');
        if (is_null($count)) {
            $count = $this->_storeConfig->getConfig(self::XML_PATH_CHECKOUT_SIDEBAR_COUNT);
            $this->setData('item_count', $count);
        }
        return $count;
    }

    /**
     * Get array of last added items
     *
     * @return array
     */
    public function getRecentItems($count = null)
    {
        if ($count === null) {
            $count = $this->getItemCount();
        }

        $items = array();
        if (!$this->getSummaryCount()) {
            return $items;
        }

        $i = 0;
        $allItems = array_reverse($this->getItems());
        foreach ($allItems as $item) {
            /* @var $item Magento_Sales_Model_Quote_Item */
            if (!$item->getProduct()->isVisibleInSiteVisibility()) {
                $productId = $item->getProduct()->getId();
                $products  = Mage::getResourceSingleton('Magento_Catalog_Model_Resource_Url')
                    ->getRewriteByProductStore(array($productId => $item->getStoreId()));
                if (!isset($products[$productId])) {
                    continue;
                }
                $urlDataObject = new Magento_Object($products[$productId]);
                $item->getProduct()->setUrlDataObject($urlDataObject);
            }

            $items[] = $item;
            if (++$i == $count) {
                break;
            }
        }

        return $items;
    }

    /**
     * Get shopping cart subtotal.
     *
     * It will include tax, if required by config settings.
     *
     * @param   bool $skipTax flag for getting price with tax or not. Ignored in case when we display just subtotal incl.tax
     * @return  decimal
     */
    public function getSubtotal($skipTax = true)
    {
        $subtotal = 0;
        $totals = $this->getTotals();
        $config = Mage::getSingleton('Magento_Tax_Model_Config');
        if (isset($totals['subtotal'])) {
            if ($config->displayCartSubtotalBoth()) {
                if ($skipTax) {
                    $subtotal = $totals['subtotal']->getValueExclTax();
                } else {
                    $subtotal = $totals['subtotal']->getValueInclTax();
                }
            } elseif($config->displayCartSubtotalInclTax()) {
                $subtotal = $totals['subtotal']->getValueInclTax();
            } else {
                $subtotal = $totals['subtotal']->getValue();
                if (!$skipTax && isset($totals['tax'])) {
                    $subtotal+= $totals['tax']->getValue();
                }
            }
        }
        return $subtotal;
    }

    /**
     * Get subtotal, including tax.
     * Will return > 0 only if appropriate config settings are enabled.
     *
     * @return decimal
     */
    public function getSubtotalInclTax()
    {
        if (!Mage::getSingleton('Magento_Tax_Model_Config')->displayCartSubtotalBoth()) {
            return 0;
        }
        return $this->getSubtotal(false);
    }

    /**
     * Add tax to amount
     *
     * @param float $price
     * @param bool $exclShippingTax
     * @return float
     */
    private function _addTax($price, $exclShippingTax=true) {
        $totals = $this->getTotals();
        if (isset($totals['tax'])) {
            if ($exclShippingTax) {
                $price += $totals['tax']->getValue()-$this->_getShippingTaxAmount();
            } else {
                $price += $totals['tax']->getValue();
            }
        }
        return $price;
    }

    /**
     * Get shipping tax amount
     *
     * @return float
     */
    protected function _getShippingTaxAmount()
    {
        $quote = $this->getCustomQuote() ? $this->getCustomQuote() : $this->getQuote();
        return $quote->getShippingAddress()->getShippingTaxAmount();
    }

    /**
     * Get shopping cart items qty based on configuration (summary qty or items qty)
     *
     * @return int | float
     */
    public function getSummaryCount()
    {
        if ($this->getData('summary_qty')) {
            return $this->getData('summary_qty');
        }
        return Mage::getSingleton('Magento_Checkout_Model_Cart')->getSummaryQty();
    }

    /**
     * Get incl/excl tax label
     *
     * @param bool $flag
     * @return string
     */
    public function getIncExcTax($flag)
    {
        $text = $this->_taxData->getIncExcText($flag);
        return $text ? ' ('.$text.')' : '';
    }

    /**
     * Check if one page checkout is available
     *
     * @return bool
     */
    public function isPossibleOnepageCheckout()
    {
        return $this->helper('Magento_Checkout_Helper_Data')->canOnepageCheckout() && !$this->getQuote()->getHasError();
    }

    /**
     * Get one page checkout page url
     *
     * @return bool
     */
    public function getCheckoutUrl()
    {
        return $this->helper('Magento_Checkout_Helper_Url')->getCheckoutUrl();
    }

    /**
     * Define if Shopping Cart Sidebar enabled
     *
     * @return bool
     */
    public function getIsNeedToDisplaySideBar()
    {
        return (bool) Mage::app()->getStore()->getConfig('checkout/sidebar/display');
    }

    /**
     * Return customer quote items
     *
     * @return array
     */
    public function getItems()
    {
        if ($this->getCustomQuote()) {
            return $this->getCustomQuote()->getAllVisibleItems();
        }

        return parent::getItems();
    }

    /*
     * Return totals from custom quote if needed
     *
     * @return array
     */
    public function getTotalsCache()
    {
        if (empty($this->_totals)) {
            $quote = $this->getCustomQuote() ? $this->getCustomQuote() : $this->getQuote();
            $this->_totals = $quote->getTotals();
        }
        return $this->_totals;
    }

    /**
     * Get cache key informative items
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $cacheKeyInfo = parent::getCacheKeyInfo();
        $cacheKeyInfo['item_renders'] = $this->_serializeRenders();
        return $cacheKeyInfo;
    }

    /**
     * Serialize renders
     *
     * @return string
     */
    protected function _serializeRenders()
    {
        $result = array();
        foreach ($this->getLayout()->getChildBlocks($this->getNameInLayout()) as $block) {
            /** @var $block Magento_Core_Block_Template */
            $result[] = implode('|', array(
                // skip $this->getNameInLayout() and '.'
                substr($block->getNameInLayout(), strlen($this->getNameInLayout()) + 1),
                get_class($block),
                $block->getTemplate()
            ));
        }
        return implode('|', $result);
    }

    /**
     * Deserialize renders from string
     *
     * @param string $renders
     * @return $this
     */
    public function deserializeRenders($renders)
    {
        if (!is_string($renders)) {
            return $this;
        }

        $renders = explode('|', $renders);
        while (!empty($renders)) {
            $template = array_pop($renders);
            $block = array_pop($renders);
            $type = array_pop($renders);
            if (!$template || !$block || !$type) {
                continue;
            }
            if (!$this->getChildBlock($type)) {
                $this->addChild($type, $block, array('template' => $template));
            }

        }

        return $this;
    }
}

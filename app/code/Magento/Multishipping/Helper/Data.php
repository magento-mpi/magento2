<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Multishipping\Helper;

/**
 * Data helper
 */
class Data extends \Magento\App\Helper\AbstractHelper
{
    /**#@+
     * Xml paths for multishipping checkout
     **/
    const XML_PATH_CHECKOUT_MULTIPLE_AVAILABLE = 'multishipping/options/checkout_multiple';
    const XML_PATH_CHECKOUT_MULTIPLE_MAXIMUM_QUANTITY = 'multishipping/options/checkout_multiple_maximum_qty';
    /**#@-*/

    /**
     * Core store config
     *
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $coreStoreConfig;

    /**
     * Checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Construct
     *
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\App\Config\ScopeConfigInterface $coreStoreConfig,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->coreStoreConfig = $coreStoreConfig;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context);
    }

    /**
     * Retrieve checkout quote
     *
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        return $this->checkoutSession->getQuote();
    }

    /**
     * Get maximum quantity allowed for shipping to multiple addresses
     *
     * @return int
     */
    public function getMaximumQty()
    {
        return (int)$this->coreStoreConfig->getValue(self::XML_PATH_CHECKOUT_MULTIPLE_MAXIMUM_QUANTITY, \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE);
    }

    /**
     * Check if multishipping checkout is available
     * There should be a valid quote in checkout session. If not, only the config value will be returned
     *
     * @return bool
     */
    public function isMultishippingCheckoutAvailable()
    {
        $quote = $this->getQuote();
        $isMultiShipping = $this->coreStoreConfig->isSetFlag(self::XML_PATH_CHECKOUT_MULTIPLE_AVAILABLE, \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE);
        if (!$quote || !$quote->hasItems()) {
            return $isMultiShipping;
        }
        return $isMultiShipping
            && !$quote->hasItemsWithDecimalQty()
            && $quote->validateMinimumAmount(true)
            && ($quote->getItemsSummaryQty() - $quote->getItemVirtualQty() > 0)
            && ($quote->getItemsSummaryQty() <= $this->getMaximumQty())
            && !$quote->hasNominalItems();
    }
}

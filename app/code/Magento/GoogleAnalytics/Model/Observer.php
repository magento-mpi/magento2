<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleAnalytics
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Google Analytics module observer
 *
 * @category   Magento
 * @package    Magento_GoogleAnalytics
 */
namespace Magento\GoogleAnalytics\Model;

class Observer
{
    /**
     * Whether the google checkout inclusion link was rendered by this observer instance
     * @var bool
     */
    protected $_isGoogleCheckoutLinkAdded = false;

    /**
     * Google analytics data
     *
     * @var Magento_GoogleAnalytics_Helper_Data
     */
    protected $_googleAnalyticsData = null;

    /**
     * @param Magento_GoogleAnalytics_Helper_Data $googleAnalyticsData
     */
    public function __construct(
        Magento_GoogleAnalytics_Helper_Data $googleAnalyticsData
    ) {
        $this->_googleAnalyticsData = $googleAnalyticsData;
    }

    /**
     * Add order information into GA block to render on checkout success pages
     *
     * @param \Magento\Event\Observer $observer
     */
    public function setGoogleAnalyticsOnOrderSuccessPageView(\Magento\Event\Observer $observer)
    {
        $orderIds = $observer->getEvent()->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }
        $block = \Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('google_analytics');
        if ($block) {
            $block->setOrderIds($orderIds);
        }
    }

    /**
     * Add google analytics tracking to google checkout shortcuts
     *
     * If there is at least one GC button on the page, there should be the script for GA/GC integration included
     * a each shortcut should track submits to GA
     * There should be no tracking if there is no GA available
     * This method assumes that the observer instance is run as a "singleton" (through \Mage::getSingleton())
     *
     * @param \Magento\Event\Observer $observer
     */
    public function injectAnalyticsInGoogleCheckoutLink(\Magento\Event\Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (!$block || !$this->_googleAnalyticsData->isGoogleAnalyticsAvailable()) {
            return;
        }

        // make sure to track google checkout "onsubmit"
        $onsubmitJs = $block->getOnsubmitJs();
        $block->setOnsubmitJs($onsubmitJs . ($onsubmitJs ? '; ' : '')
        . '_gaq.push(function() {var pageTracker = _gaq._getAsyncTracker(); setUrchinInputCode(pageTracker);});');

        // add a link that includes google checkout/analytics script, to the first instance of the link block
        if ($this->_isGoogleCheckoutLinkAdded) {
            return;
        }
        $beforeHtml = $block->getBeforeHtml();
        $protocol = \Mage::app()->getStore()->isCurrentlySecure() ? 'https' : 'http';
        $block->setBeforeHtml($beforeHtml . '<script src="' . $protocol
            . '://checkout.google.com/files/digital/ga_post.js" type="text/javascript"></script>'
        );
        $this->_isGoogleCheckoutLinkAdded = true;
    }
}

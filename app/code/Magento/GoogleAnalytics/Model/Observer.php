<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleAnalytics
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleAnalytics\Model;

use Magento\Event\Observer as EventObserver;

/**
 * Google Analytics module observer
 *
 * @category   Magento
 * @package    Magento_GoogleAnalytics
 */
class Observer
{
    /**
     * Google analytics data
     *
     * @var \Magento\GoogleAnalytics\Helper\Data
     */
    protected $_googleAnalyticsData = null;

    /**
     * @var \Magento\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\View\LayoutInterface $layout
     * @param \Magento\GoogleAnalytics\Helper\Data $googleAnalyticsData
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\View\LayoutInterface $layout,
        \Magento\GoogleAnalytics\Helper\Data $googleAnalyticsData
    ) {
        $this->_googleAnalyticsData = $googleAnalyticsData;
        $this->_layout = $layout;
        $this->_storeManager = $storeManager;
    }

    /**
     * Add order information into GA block to render on checkout success pages
     *
     * @param EventObserver $observer
     * @return void
     */
    public function setGoogleAnalyticsOnOrderSuccessPageView(EventObserver $observer)
    {
        $orderIds = $observer->getEvent()->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }
        $block = $this->_layout->getBlock('google_analytics');
        if ($block) {
            $block->setOrderIds($orderIds);
        }
    }
}

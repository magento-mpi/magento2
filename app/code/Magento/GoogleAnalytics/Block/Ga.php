<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleAnalytics\Block;

/**
 * GoogleAnalytics Page Block
 */
class Ga extends \Magento\Framework\View\Element\Template
{
    /**
     * Google analytics data
     *
     * @var \Magento\GoogleAnalytics\Helper\Data
     */
    protected $_googleAnalyticsData = null;

    /**
     * @var \Magento\Sales\Model\Resource\Order\CollectionFactory
     */
    protected $_salesOrderCollection;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\Resource\Order\CollectionFactory $salesOrderCollection
     * @param \Magento\GoogleAnalytics\Helper\Data $googleAnalyticsData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\Resource\Order\CollectionFactory $salesOrderCollection,
        \Magento\GoogleAnalytics\Helper\Data $googleAnalyticsData,
        array $data = array()
    ) {
        $this->_googleAnalyticsData = $googleAnalyticsData;
        $this->_salesOrderCollection = $salesOrderCollection;
        parent::__construct($context, $data);
    }

    /**
     * Get config
     *
     * @param string $path
     * @return mixed
     */
    public function getConfig($path)
    {
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get a specific page name (may be customized via layout)
     *
     * @return string|null
     */
    public function getPageName()
    {
        return $this->_getData('page_name');
    }

    /**
     * Render regular page tracking javascript code
     * The custom "page name" may be set from layout or somewhere else. It must start from slash.
     *
     * @param string $accountId
     * @return string
     * @link https://developers.google.com/analytics/devguides/collection/analyticsjs/method-reference#set
     * @link https://developers.google.com/analytics/devguides/collection/analyticsjs/method-reference#gaObjectMethods
     */
    public function getPageTrackingCode($accountId)
    {
        $pageName = trim($this->getPageName());
        $optPageURL = '';
        if ($pageName && substr($pageName, 0, 1) == '/' && strlen($pageName) > 1) {
            $optPageURL = ", '{$this->escapeJsQuote($pageName)}'";
        }

        return "\nga('create', '{$this->escapeJsQuote(
            $accountId
        )}', 'auto');\nga('send', 'pageview'{$optPageURL});\n";
    }

    /**
     * Render information about specified orders and their items
     *
     * @Link https://developers.google.com/analytics/devguides/collection/gajs/methods/gaJSApiEcommerce?csw=1#_gat.GA_Tracker_._addItem
     * @link https://developers.google.com/analytics/devguides/collection/gajs/methods/gaJSApiEcommerce?csw=1#_gat.GA_Tracker_._addTrans
     * @return string|void
     */
    public function getOrdersTrackingCode()
    {
        $orderIds = $this->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }

        $collection = $this->_salesOrderCollection->create();
        $collection->addFieldToFilter('entity_id', array('in' => $orderIds));
        $result = [];

        $result[] = "ga('require', 'ecommerce', 'ecommerce.js');";
        foreach ($collection as $order) {
            if ($order->getIsVirtual()) {
                $address = $order->getBillingAddress();
            } else {
                $address = $order->getShippingAddress();
            }
            $result[] = sprintf(
                "ga('ecommerce:addTransaction', {
                    'id': '%s',
                    'affiliation': '%s',
                    'revenue': '%s',
                    'shipping': '%s',
                    'tax': '%s'
                });",
                $order->getIncrementId(),
                $this->escapeJsQuote($this->_storeManager->getStore()->getFrontendName()),
                $order->getBaseGrandTotal(),
                $order->getBaseShippingAmount(),
                $order->getBaseTaxAmount()
            );
            foreach ($order->getAllVisibleItems() as $item) {
                $result[] = sprintf(
                    "ga('ecommerce:addItem', {
                        'id': '%s',
                        'name': '%s',
                        'sku': '%s',
                        'category': '%s',
                        'price': '%s',
                        'quantity': '%s'
                    });",
                    $order->getIncrementId(),
                    $this->escapeJsQuote($item->getName()),
                    $this->escapeJsQuote($item->getSku()),
                    null, // there is no "category" defined for the order item
                    $item->getBasePrice(),
                    $item->getQtyOrdered()
                );
            }
            $result[] = "ga('ecommerce:send')";
        }
        return implode("\n", $result);
    }

    /**
     * Render GA tracking scripts
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_googleAnalyticsData->isGoogleAnalyticsAvailable()) {
            return '';
        }

        return parent::_toHtml();
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleAnalytics
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleAnalytics\Block;

/**
 * GoogleAnalitics Page Block
 *
 * @category   Magento
 * @package    Magento_GoogleAnalytics
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Ga extends \Magento\View\Element\Template
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
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\Resource\Order\CollectionFactory $salesOrderCollection
     * @param \Magento\GoogleAnalytics\Helper\Data $googleAnalyticsData
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
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
        return $this->_storeConfig->getConfig($path);
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
     * @link http://code.google.com/apis/analytics/docs/gaJS/gaJSApiBasicConfiguration.html#_gat.GA_Tracker_._trackPageview
     * @link http://code.google.com/apis/analytics/docs/gaJS/gaJSApi_gaq.html
     */
    public function getPageTrackingCode($accountId)
    {
        $pageName = trim($this->getPageName());
        $optPageURL = '';
        if ($pageName && substr($pageName, 0, 1) == '/' && strlen($pageName) > 1) {
            $optPageURL = ", '{$this->escapeJsQuote($pageName)}'";
        }
        return "\n_gaq.push(['_setAccount', '{$this->escapeJsQuote(
            $accountId
        )}']);\n_gaq.push(['_trackPageview'{$optPageURL}]);\n";
    }

    /**
     * Render information about specified orders and their items
     *
     * @link http://code.google.com/apis/analytics/docs/gaJS/gaJSApiEcommerce.html#_gat.GA_Tracker_._addTrans
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
        $result = array();
        foreach ($collection as $order) {
            if ($order->getIsVirtual()) {
                $address = $order->getBillingAddress();
            } else {
                $address = $order->getShippingAddress();
            }
            $result[] = sprintf(
                "_gaq.push(['_addTrans', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']);",
                $order->getIncrementId(),
                $this->escapeJsQuote($this->_storeManager->getStore()->getFrontendName()),
                $order->getBaseGrandTotal(),
                $order->getBaseTaxAmount(),
                $order->getBaseShippingAmount(),
                $this->escapeJsQuote($this->escapeHtml($address->getCity())),
                $this->escapeJsQuote($this->escapeHtml($address->getRegion())),
                $this->escapeJsQuote($this->escapeHtml($address->getCountry()))
            );
            foreach ($order->getAllVisibleItems() as $item) {
                $result[] = sprintf(
                    "_gaq.push(['_addItem', '%s', '%s', '%s', '%s', '%s', '%s']);",
                    $order->getIncrementId(),
                    $this->escapeJsQuote($item->getSku()),
                    $this->escapeJsQuote($item->getName()),
                    null,
                    $item->getBasePrice(),
                    $item->getQtyOrdered()
                );
            }
            $result[] = "_gaq.push(['_trackTrans']);";
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

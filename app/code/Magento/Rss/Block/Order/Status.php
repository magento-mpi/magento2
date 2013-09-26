<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Review form block
 */
class Magento_Rss_Block_Order_Status extends Magento_Core_Block_Template
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * @var Magento_Rss_Model_RssFactory
     */
    protected $_rssFactory;

    /**
     * @var Magento_Rss_Model_Resource_OrderFactory
     */
    protected $_orderFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Rss_Model_RssFactory $rssFactory
     * @param Magento_Rss_Model_Resource_OrderFactory $orderFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Rss_Model_RssFactory $rssFactory,
        Magento_Rss_Model_Resource_OrderFactory $orderFactory,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_rssFactory = $rssFactory;
        $this->_orderFactory = $orderFactory;
        parent::__construct($coreData, $context, $data);
    }

    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
        $this->setCacheKey('rss_order_status_' . $this->getRequest()->getParam('data'));
        $this->setCacheLifetime(600);
    }

    protected function _toHtml()
    {
        /** @var $rssObj Magento_Rss_Model_Rss */
        $rssObj = $this->_rssFactory->create();
        $order = $this->_coreRegistry->registry('current_order');
        if (!$order) {
            return '';
        }
        $title = __('Order # %1 Notification(s)', $order->getIncrementId());
        $newUrl = $this->_urlBuilder->getUrl('sales/order/view', array('order_id' => $order->getId()));
        $rssObj->_addHeader(array(
            'title'       => $title,
            'description' => $title,
            'link'        => $newUrl,
            'charset'     => 'UTF-8',
        ));
        /** @var $resourceModel Magento_Rss_Model_Resource_Order */
        $resourceModel = $this->_orderFactory->create();
        $results = $resourceModel->getAllCommentCollection($order->getId());
        if ($results) {
            foreach ($results as $result) {
                $urlAppend = 'view';
                $type = $result['entity_type_code'];
                if ($type && $type != 'order') {
                   $urlAppend = $type;
                }
                $type  = __(ucwords($type));
                $title = __('Details for %1 #%2', $type, $result['increment_id']);
                $description = '<p>'
                    . __('Notified Date: %1<br/>',$this->formatDate($result['created_at']))
                    . __('Comment: %1<br/>',$result['comment'])
                    . '</p>';
                $url = $this->_urlBuilder->getUrl('sales/order/' . $urlAppend, array('order_id' => $order->getId()));
                $rssObj->_addEntry(array(
                    'title'         => $title,
                    'link'          => $url,
                    'description'   => $description,
                ));
            }
        }
        $title = __('Order #%1 created at %2', $order->getIncrementId(), $this->formatDate($order->getCreatedAt()));
        $url = $this->_urlBuilder->getUrl('sales/order/view',array('order_id' => $order->getId()));
        $description = '<p>'
            . __('Current Status: %1<br/>', $order->getStatusLabel())
            . __('Total: %1<br/>', $order->formatPrice($order->getGrandTotal()))
            . '</p>';
        $rssObj->_addEntry(array(
            'title'         => $title,
            'link'          => $url,
            'description'   => $description,
        ));
        return $rssObj->createRssXml();
    }
}

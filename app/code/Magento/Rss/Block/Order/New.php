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
) */
class Magento_Rss_Block_Order_New extends Magento_Core_Block_Abstract
{
    /**
     * Adminhtml data
     *
     * @var Magento_Backend_Helper_Data
     */
    protected $_adminhtmlData = null;

    /**
     * @var Magento_Rss_Model_RssFactory
     */
    protected $_rssFactory;

    /**
     * @var Magento_Sales_Model_OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var Magento_Core_Model_Resource_Iterator
     */
    protected $_resourceIterator;

    /**
     * @param Magento_Backend_Helper_Data $adminhtmlData
     * @param Magento_Core_Block_Context $context
     * @param Magento_Rss_Model_RssFactory $rssFactory
     * @param Magento_Sales_Model_OrderFactory $orderFactory
     * @param Magento_Core_Model_Resource_Iterator $resourceIterator
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Helper_Data $adminhtmlData,
        Magento_Core_Block_Context $context,
        Magento_Rss_Model_RssFactory $rssFactory,
        Magento_Sales_Model_OrderFactory $orderFactory,
        Magento_Core_Model_Resource_Iterator $resourceIterator,
        array $data = array()
    ) {
        $this->_adminhtmlData = $adminhtmlData;
        $this->_rssFactory = $rssFactory;
        $this->_orderFactory = $orderFactory;
        $this->_resourceIterator = $resourceIterator;
        parent::__construct($context, $data);
    }

    protected function _toHtml()
    {
        /** @var $order Magento_Sales_Model_Order */
        $order = $this->_orderFactory->create();
        $passDate = $order->getResource()->formatDate(mktime(0, 0, 0, date('m'), date('d')-7));
        $newUrl = $this->_adminhtmlData->getUrl(
            'adminhtml/sales_order', array('_secure' => true, '_nosecret' => true)
        );
        $title = __('New Orders');

        /** @var $rssObj Magento_Rss_Model_Rss */
        $rssObj = $this->_rssFactory->create();
        $rssObj->_addHeader(array(
            'title'       => $title,
            'description' => $title,
            'link'        => $newUrl,
            'charset'     => 'UTF-8',
        ));

        /** @var $collection Magento_Sales_Model_Resource_Order_Collection */
        $collection = $order->getCollection();
        $collection->addAttributeToFilter('created_at', array('date'=>true, 'from'=> $passDate))
            ->addAttributeToSort('created_at', 'desc');

        $detailBlock = $this->_layout->getBlockSingleton('Magento_Rss_Block_Order_Details');
        $this->_eventManager->dispatch('rss_order_new_collection_select', array('collection' => $collection));
        $this->_resourceIterator->walk(
            $collection->getSelect(),
            array(array($this, 'addNewOrderXmlCallback')),
            array('rssObj' => $rssObj, 'order' => $order , 'detailBlock' => $detailBlock)
        );
        return $rssObj->createRssXml();
    }

    public function addNewOrderXmlCallback($args)
    {
        /** @var $rssObj Magento_Rss_Model_Rss */
        $rssObj = $args['rssObj'];
        /** @var $order Magento_Sales_Model_Order */
        $order = $args['order'];
        /** @var $detailBlock Magento_Rss_Block_Order_Details */
        $detailBlock = $args['detailBlock'];
        $order->reset()->load($args['row']['entity_id']);
        if ($order && $order->getId()) {
            $title = __('Order #%1 created at %2', $order->getIncrementId(), $this->formatDate($order->getCreatedAt()));
            $url = $this->_adminhtmlData->getUrl(
                'adminhtml/sales_order/view',
                array(
                    '_secure' => true,
                    'order_id' => $order->getId(),
                    '_nosecret' => true
                )
            );
            $detailBlock->setOrder($order);
            $rssObj->_addEntry(array(
                'title'         => $title,
                'link'          => $url,
                'description'   => $detailBlock->toHtml()
            ));
        }
    }
}

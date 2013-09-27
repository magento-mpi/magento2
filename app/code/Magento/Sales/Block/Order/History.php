<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales order history block
 */
class Magento_Sales_Block_Order_History extends Magento_Core_Block_Template
{
    /**
     * @var string
     */
    protected $_template = 'order/history.phtml';

    /**
     * @var Magento_Sales_Model_Resource_Order_CollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @var Magento_Sales_Model_Order_Config
     */
    protected $_orderConfig;

    /**
     * @var Magento_Core_Model_App
     */
    protected $_coreApp;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Sales_Model_Resource_Order_CollectionFactory $orderCollectionFactory
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Sales_Model_Order_Config $orderConfig
     * @param Magento_Core_Model_App $coreApp
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Sales_Model_Resource_Order_CollectionFactory $orderCollectionFactory,
        Magento_Customer_Model_Session $customerSession,
        Magento_Sales_Model_Order_Config $orderConfig,
        Magento_Core_Model_App $coreApp,
        array $data = array()
    ) {
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->_orderConfig = $orderConfig;
        $this->_coreApp = $coreApp;
        parent::__construct($coreData, $context, $data);
    }

    protected function _construct()
    {
        parent::_construct();


        $orders = $this->_orderCollectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', $this->_customerSession->getCustomer()->getId())
            ->addFieldToFilter('state', array('in' => $this->_orderConfig->getVisibleOnFrontStates()))
            ->setOrder('created_at', 'desc');

        $this->setOrders($orders);

        if ($this->_coreApp->getFrontController()->getAction()) {
            $this->_coreApp->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(
                __('My Orders')
            );
        }
    }

    /**
     * @return $this|Magento_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('Magento_Page_Block_Html_Pager', 'sales.order.history.pager')
            ->setCollection($this->getOrders());
        $this->setChild('pager', $pager);
        $this->getOrders()->load();
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param object $order
     * @return string
     */
    public function getViewUrl($order)
    {
        return $this->getUrl('*/*/view', array('order_id' => $order->getId()));
    }

    /**
     * @param object $order
     * @return string
     */
    public function getTrackUrl($order)
    {
        return $this->getUrl('*/*/track', array('order_id' => $order->getId()));
    }

    /**
     * @param object $order
     * @return string
     */
    public function getReorderUrl($order)
    {
        return $this->getUrl('*/*/reorder', array('order_id' => $order->getId()));
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }
}

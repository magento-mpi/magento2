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
namespace Magento\Sales\Block\Order;

class History extends \Magento\Core\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'order/history.phtml';

    /**
     * @var \Magento\Sales\Model\Resource\Order\CollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /**
     * @var \Magento\Core\Model\App
     */
    protected $_coreApp;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Sales\Model\Resource\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Core\Model\App $coreApp
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Sales\Model\Resource\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Core\Model\App $coreApp,
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
     * @return $this|\Magento\Core\Block\AbstractBlock
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('Magento\Page\Block\Html\Pager', 'sales.order.history.pager')
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

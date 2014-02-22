<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Block\Onepage;

/**
 * One page checkout success page
 */
class Success extends \Magento\View\Element\Template
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\Order\Config $orderConfig,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_orderFactory = $orderFactory;
        $this->_orderConfig = $orderConfig;
        $this->_isScopePrivate = true;
    }

    /**
     * See if the order has state, visible on frontend
     *
     * @return bool
     */
    public function isOrderVisible()
    {
        return (bool)$this->_getData('is_order_visible');
    }

    /**
     * Render additional order information lines and return result html
     *
     * @return string
     */
    public function getAdditionalInfoHtml()
    {
        return $this->_layout->renderElement('order.success.additional.info');
    }

    /**
     * Initialize data and prepare it for output
     */
    protected function _beforeToHtml()
    {
        $this->_prepareLastOrder();
        return parent::_beforeToHtml();
    }

    /**
     * Get last order ID from session, fetch it and check whether it can be viewed, printed etc
     */
    protected function _prepareLastOrder()
    {
        $orderId = $this->_checkoutSession->getLastOrderId();
        if ($orderId) {
            $order = $this->_orderFactory->create()->load($orderId);
            if ($order->getId()) {
                $isVisible = !in_array($order->getState(), $this->_orderConfig->getInvisibleOnFrontStates());
                $this->addData(
                    array(
                        'is_order_visible' => $isVisible,
                        'view_order_url' => $this->getUrl('sales/order/view/', array('order_id' => $orderId)),
                        'print_url' => $this->getUrl('sales/order/print', array('order_id' => $orderId)),
                        'can_print_order' => $isVisible,
                        'can_view_order' => $this->_customerSession->isLoggedIn() && $isVisible,
                        'order_id' => $order->getIncrementId()
                    )
                );
            }
        }
    }
}

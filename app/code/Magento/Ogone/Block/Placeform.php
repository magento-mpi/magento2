<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Ogone
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Ogone\Block;

class Placeform extends \Magento\Core\Block\Template
{
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_salesOrderFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Ogone\Model\Api
     */
    protected $_ogoneApi;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Ogone\Model\Api $ogoneApi
     * @param \Magento\Sales\Model\OrderFactory $salesOrderFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Ogone\Model\Api $ogoneApi,
        \Magento\Sales\Model\OrderFactory $salesOrderFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_ogoneApi = $ogoneApi;
        $this->_salesOrderFactory = $salesOrderFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Get checkout session namespace
     *
     * @return \Magento\Checkout\Model\Session
     */
    public function getCheckout()
    {
        return $this->_checkoutSession;
    }

    /**
     * Return order instance with loaded onformation by increment id
     *
     * @return \Magento\Sales\Model\Order
     */
    protected function _getOrder()
    {
        if ($this->getOrder()) {
            $order = $this->getOrder();
        } else if ($this->_checkoutSession->getLastRealOrderId()) {
            $order = $this->_salesOrderFactory->create()
                ->loadByIncrementId($this->_checkoutSession->getLastRealOrderId());
        } else {
            return null;
        }
        return $order;
    }

    /**
     * Get Form data by using ogone payment api
     *
     * @return array
     */
    public function getFormData()
    {
        return $this->_ogoneApi->getFormFields($this->_getOrder());
    }

    /**
     * Getting gateway url
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->_ogoneApi->getConfig()->getGatewayPath();
    }
}

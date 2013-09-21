<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Ogone
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Ogone_Block_Placeform extends Magento_Core_Block_Template
{
    /**
     * @var Magento_Sales_Model_OrderFactory
     */
    protected $_salesOrderFactory;

    /**
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * @var Magento_Ogone_Model_Api
     */
    protected $_ogoneApi;

    /**
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param Magento_Ogone_Model_Api $ogoneApi
     * @param Magento_Sales_Model_OrderFactory $salesOrderFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Ogone_Model_Api $ogoneApi,
        Magento_Sales_Model_OrderFactory $salesOrderFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
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
     * @return Magento_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return $this->_checkoutSession;
    }

    /**
     * Return order instance with loaded onformation by increment id
     *
     * @return Magento_Sales_Model_Order
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

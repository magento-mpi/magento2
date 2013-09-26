<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Checkout_Block_Success extends Magento_Core_Block_Template
{
    /**
     * @var Magento_Sales_Model_OrderFactory
     */
    protected $_orderFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Sales_Model_OrderFactory $orderFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Sales_Model_OrderFactory $orderFactory,
        array $data = array()
    ) {
        $this->_orderFactory = $orderFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * @return int
     */
    public function getRealOrderId()
    {
        /** @var Magento_Sales_Model_Order $order */
        $order = $this->_orderFactory()->create()->load($this->getLastOrderId());
        return $order->getIncrementId();
    }
}

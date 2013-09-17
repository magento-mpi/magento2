<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Refund to customer balance functionality block
 *
 */
class Magento_CustomerBalance_Block_Adminhtml_Sales_Order_Creditmemo_Controls
 extends Magento_Core_Block_Template
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Check whether refund to customerbalance is available
     *
     * @return bool
     */
    public function canRefundToCustomerBalance()
    {
        if ($this->_coreRegistry->registry('current_creditmemo')->getOrder()->getCustomerIsGuest()) {
            return false;
        }
        return true;
    }

    /**
     * Check whether real amount can be refunded to customer balance
     *
     * @return bool
     */
    public function canRefundMoneyToCustomerBalance()
    {
        if (!$this->_coreRegistry->registry('current_creditmemo')->getGrandTotal()) {
            return false;
        }

        if ($this->_coreRegistry->registry('current_creditmemo')->getOrder()->getCustomerIsGuest()) {
            return false;
        }
        return true;
    }

    /**
     * Prepopulate amount to be refunded to customerbalance
     *
     * @return float
     */
    public function getReturnValue()
    {
        $max = $this->_coreRegistry->registry('current_creditmemo')->getCustomerBalanceReturnMax();
        if ($max) {
            return $max;
        }
        return 0;
    }
}

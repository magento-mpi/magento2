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
namespace Magento\CustomerBalance\Block\Adminhtml\Sales\Order\Creditmemo;

class Controls
 extends \Magento\Core\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
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

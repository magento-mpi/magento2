<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerBalance\Block\Adminhtml\Sales\Order\Creditmemo;

use Magento\Core\Model\Registry;
use Magento\View\Element\Template;
use Magento\View\Element\Template\Context;

/**
 * Refund to customer balance functionality block
 */
class Controls extends Template
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(Context $context, Registry $registry, array $data = array())
    {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Check whether refund to customer balance is available
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
     * Populate amount to be refunded to customer balance
     *
     * @return float
     */
    public function getReturnValue()
    {
        $max = $this->_coreRegistry->registry('current_creditmemo')->getCustomerBalanceReturnMax();

        //We want to subtract the reward balance when returning to the customer
        $rewardCurrencyBalance = $this->_coreRegistry->registry('current_creditmemo')->getRewardCurrencyAmount();
        if ($rewardCurrencyBalance > 0 && $rewardCurrencyBalance < $max) {
            $max = $max - $rewardCurrencyBalance;
        }

        if ($max) {
            return $max;
        }
        return 0;
    }
}

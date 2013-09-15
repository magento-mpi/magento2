<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward points refund block in creditmemo
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Block\Adminhtml\Sales\Order\Creditmemo;

class Reward
    extends \Magento\Adminhtml\Block\Template
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Getter
     *
     * @return \Magento\Sales\Model\Order\Creditmemo
     */
    public function getCreditmemo()
    {
        return $this->_coreRegistry->registry('current_creditmemo');
    }

    /**
     * Check whether can refund reward points to customer
     *
     * @return boolean
     */
    public function canRefundRewardPoints()
    {
        if ($this->getCreditmemo()->getOrder()->getCustomerIsGuest()) {
            return false;
        }
        if ($this->getCreditmemo()->getRewardPointsBalance() <= 0) {
            return false;
        }
        return true;
    }

    /**
     * Return maximum points balance to refund
     *
     * @return integer
     */
    public function getRefundRewardPointsBalance()
    {
        return (int)$this->getCreditmemo()->getRewardPointsBalance();
    }
}

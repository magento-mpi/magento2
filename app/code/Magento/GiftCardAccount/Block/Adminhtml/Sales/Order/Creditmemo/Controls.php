<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Block\Adminhtml\Sales\Order\Creditmemo;

class Controls extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function canRefundToCustomerBalance()
    {
        if (!$this->_coreRegistry->registry('current_creditmemo')->getGiftCardsAmount()) {
            return false;
        }

        if ($this->_coreRegistry->registry('current_creditmemo')->getOrder()->getCustomerIsGuest()) {
            return false;
        }
        return true;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Adminhtml\Order;

use Magento\Payment\Model\Info;

/**
 * Adminhtml sales order payment information
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Payment extends \Magento\Backend\Block\Template
{
    /**
     * Payment data
     *
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentData = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Payment\Helper\Data $paymentData,
        array $data = array()
    ) {
        $this->_paymentData = $paymentData;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve required options from parent
     *
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            throw new \Magento\Framework\Model\Exception(__('Invalid parent block for this block'));
        }
        $this->setPayment($this->getParentBlock()->getOrder()->getPayment());
        parent::_beforeToHtml();
    }

    /**
     * Set payment
     *
     * @param Info $payment
     * @return $this
     */
    public function setPayment($payment)
    {
        $paymentInfoBlock = $this->_paymentData->getInfoBlock($payment, $this->getLayout());
        $this->setChild('info', $paymentInfoBlock);
        $this->setData('payment', $payment);
        return $this;
    }

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->getChildHtml('info');
    }
}

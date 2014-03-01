<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales order payment information
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Order;

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
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            throw new \Magento\Core\Exception(__('Invalid parent block for this block'));
        }
        $this->setPayment($this->getParentBlock()->getOrder()->getPayment());
        parent::_beforeToHtml();
    }

    /**
     * Set payment
     *
     * @param $payment
     * @return $this
     */
    public function setPayment($payment)
    {
        $paymentInfoBlock = $this->_paymentData->getInfoBlock($payment);
        $this->setChild('info', $paymentInfoBlock);
        $this->setData('payment', $payment);
        return $this;
    }

    protected function _toHtml()
    {
        return $this->getChildHtml('info');
    }
}

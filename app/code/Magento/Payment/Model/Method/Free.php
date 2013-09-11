<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Free payment method
 *
 * @category   Magento
 * @package    Magento_Payment
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Payment\Model\Method;

class Free extends \Magento\Payment\Model\Method\AbstractMethod
{
    /**
     * XML Paths for configuration constants
     */
    const XML_PATH_PAYMENT_FREE_ACTIVE = 'payment/free/active';
    const XML_PATH_PAYMENT_FREE_ORDER_STATUS = 'payment/free/order_status';
    const XML_PATH_PAYMENT_FREE_PAYMENT_ACTION = 'payment/free/payment_action';

    /**
     * Payment Method features
     * @var bool
     */
    protected $_canAuthorize = true;

    /**
     * Payment code name
     *
     * @var string
     */
    protected $_code = 'free';

    /**
     * Check whether method is available
     *
     * @param \Magento\Sales\Model\Quote|null $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        return parent::isAvailable($quote) && !empty($quote)
            && \Mage::app()->getStore()->roundPrice($quote->getGrandTotal()) == 0;
    }

    /**
     * Get config payment action, do nothing if status is pending
     *
     * @return string|null
     */
    public function getConfigPaymentAction()
    {
        return $this->getConfigData('order_status') == 'pending' ? null : parent::getConfigPaymentAction();
    }
}

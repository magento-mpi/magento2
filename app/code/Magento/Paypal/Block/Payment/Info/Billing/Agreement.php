<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Billing Agreement info block
 */
namespace Magento\Paypal\Block\Payment\Info\Billing;

class Agreement extends \Magento\Payment\Block\Info
{
    /**
     * Add reference id to payment method information
     *
     * @param \Magento\Object|array|null $transport
     * @return \Magento\Object
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $info = $this->getInfo();
        $referenceID = $info->getAdditionalInformation(
            \Magento\Paypal\Model\Payment\Method\Billing\AbstractAgreement::PAYMENT_INFO_REFERENCE_ID
        );
        $transport = new \Magento\Object(array((string)__('Reference ID') => $referenceID,));
        $transport = parent::_prepareSpecificInformation($transport);

        return $transport;
    }
}

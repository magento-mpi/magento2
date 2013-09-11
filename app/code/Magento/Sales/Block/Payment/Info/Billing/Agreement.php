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
 * Sales Billing Agreement info block
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Payment\Info\Billing;

class Agreement extends \Magento\Payment\Block\Info
{
/**
     * Add reference id to payment method information
     *
     * @param \Magento\Object|array $transport
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $info = $this->getInfo();
        $referenceID = $info->getAdditionalInformation(
            \Magento\Sales\Model\Payment\Method\Billing\AgreementAbstract::PAYMENT_INFO_REFERENCE_ID
        );
        $transport = new \Magento\Object(array((string)__('Reference ID') => $referenceID,));
        $transport = parent::_prepareSpecificInformation($transport);

        return $transport;
    }
}

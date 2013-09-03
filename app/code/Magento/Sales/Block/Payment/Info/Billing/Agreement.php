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
class Magento_Sales_Block_Payment_Info_Billing_Agreement extends Magento_Payment_Block_Info
{
/**
     * Add reference id to payment method information
     *
     * @param Magento_Object|array $transport
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $info = $this->getInfo();
        $referenceID = $info->getAdditionalInformation(
            Magento_Sales_Model_Payment_Method_Billing_AgreementAbstract::PAYMENT_INFO_REFERENCE_ID
        );
        $transport = new Magento_Object(array((string)__('Reference ID') => $referenceID,));
        $transport = parent::_prepareSpecificInformation($transport);

        return $transport;
    }
}

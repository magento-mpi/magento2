<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Payment_Block_Info_Ccsave extends Mage_Payment_Block_Info_Cc
{
    /**
     * Show name on card, expiration date and full cc number
     *
     * Expiration date and full number will show up only in secure mode (only for admin, not in emails or pdfs)
     *
     * @param Varien_Object|array $transport
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $info = $this->getInfo();
        $transport = new Varien_Object(array(__('Name on the Card') => $info->getCcOwner(),));
        $transport = parent::_prepareSpecificInformation($transport);
        if (!$this->getIsSecureMode()) {
            $transport->addData(array(
                __('Expiration Date') => $this->_formatCardDate(
                    $info->getCcExpYear(), $this->getCcExpMonth()
                ),
                __('Credit Card Number') => $info->getCcNumber(),
            ));
        }
        return $transport;
    }
}

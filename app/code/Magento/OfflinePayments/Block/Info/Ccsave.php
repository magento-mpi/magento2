<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\OfflinePayments\Block\Info;

class Ccsave extends \Magento\Payment\Block\Info\Cc
{
    /**
     * Show name on card, expiration date and full cc number
     *
     * Expiration date and full number will show up only in secure mode (only for admin, not in emails or pdfs)
     *
     * @param \Magento\Framework\Object|array $transport
     * @return \Magento\Framework\Object
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $info = $this->getInfo();
        $transport = new \Magento\Framework\Object(array((string)__('Name on the Card') => $info->getCcOwner()));
        $transport = parent::_prepareSpecificInformation($transport);
        if (!$this->getIsSecureMode()) {
            $transport->addData(
                array(
                    (string)__(
                        'Expiration Date'
                    ) => $this->_formatCardDate(
                        $info->getCcExpYear(),
                        $this->getCcExpMonth()
                    ),
                    (string)__('Credit Card Number') => $info->getCcNumber()
                )
            );
        }
        return $transport;
    }
}

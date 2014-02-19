<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\OfflinePaymentMethods\Block\Info;

class Purchaseorder extends \Magento\Payment\Block\Info
{
    protected $_template = 'Magento_OfflinePaymentMethods::info/purchaseorder.phtml';

    public function toPdf()
    {
        $this->setTemplate('Magento_OfflinePaymentMethods::info/pdf/purchaseorder.phtml');
        return $this->toHtml();
    }
}

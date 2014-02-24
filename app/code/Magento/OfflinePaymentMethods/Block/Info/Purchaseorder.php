<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\OfflinePaymentMethods\Block\Info;

class Purchaseorder extends \Magento\Payment\Block\Info
{
    /**
     * @var string
     */
    protected $_template = 'Magento_OfflinePaymentMethods::info/purchaseorder.phtml';

    /**
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('Magento_OfflinePaymentMethods::info/pdf/purchaseorder.phtml');
        return $this->toHtml();
    }
}

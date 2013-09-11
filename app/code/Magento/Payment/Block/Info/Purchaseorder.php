<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Payment\Block\Info;

class Purchaseorder extends \Magento\Payment\Block\Info
{
    protected $_template = 'Magento_Payment::info/purchaseorder.phtml';

    public function toPdf()
    {
        $this->setTemplate('Magento_Payment::info/pdf/purchaseorder.phtml');
        return $this->toHtml();
    }
}

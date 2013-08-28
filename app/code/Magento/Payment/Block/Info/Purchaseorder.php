<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Payment_Block_Info_Purchaseorder extends Magento_Payment_Block_Info
{
    protected $_template = 'Magento_Payment::info/purchaseorder.phtml';

    public function toPdf()
    {
        $this->setTemplate('Magento_Payment::info/pdf/purchaseorder.phtml');
        return $this->toHtml();
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Payment_Block_Info_Purchaseorder extends Mage_Payment_Block_Info
{
    protected $_template = 'Mage_Payment::info/purchaseorder.phtml';

    public function toPdf()
    {
        $this->setTemplate('Mage_Payment::info/pdf/purchaseorder.phtml');
        return $this->toHtml();
    }
}

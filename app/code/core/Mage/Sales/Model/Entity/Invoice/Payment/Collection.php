<?php

/**
 * Quote addresses collection
 *
 * @package    Mage
 * @subpackage Sales
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @author     Moshe Gurvich <moshe@varien.com>
 */

class Mage_Sales_Model_Entity_Invoice_Payment_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    public function __construct()
    {
        $this->setEntity(Mage::getResourceSingleton('sales/invoice_payment'));
        $this->setObject('sales/invoice_payment');
    }

    public function setInvoiceFilter($invoiceId)
    {
        $this->addAttributeToFilter('parent_id', $invoiceId);
        return $this;
    }
}
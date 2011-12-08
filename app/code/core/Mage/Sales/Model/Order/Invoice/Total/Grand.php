<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Sales_Model_Order_Invoice_Total_Grand extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        /**
         * Check order grand total and invoice amounts
         */
        if ($invoice->isLast()) {
            //
        }
        return $this;
    }
}

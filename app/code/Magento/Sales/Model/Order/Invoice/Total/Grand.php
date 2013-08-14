<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Sales_Model_Order_Invoice_Total_Grand extends Magento_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Magento_Sales_Model_Order_Invoice $invoice)
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

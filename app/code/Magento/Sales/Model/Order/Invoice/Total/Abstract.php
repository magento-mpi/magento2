<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Base class for invoice total
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Sales_Model_Order_Invoice_Total_Abstract extends Magento_Sales_Model_Order_Total_Abstract
{
    /**
     * Collect invoice subtotal
     *
     * @param Magento_Sales_Model_Order_Invoice $invoice
     * @return Magento_Sales_Model_Order_Invoice_Total_Abstract
     */
    public function collect(Magento_Sales_Model_Order_Invoice $invoice)
    {
        return $this;
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tax discount totals calculation model
 */
class Magento_Tax_Model_Sales_Total_Quote_Discount extends Magento_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Calculate discount tac amount
     *
     * @param   Magento_Sales_Model_Quote_Address $address
     * @return  Magento_Tax_Model_Sales_Total_Quote
     */
    public function collect(Magento_Sales_Model_Quote_Address $address)
    {
//        echo 'discount';
    }
}

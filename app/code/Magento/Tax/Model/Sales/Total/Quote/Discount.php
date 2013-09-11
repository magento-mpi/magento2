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
namespace Magento\Tax\Model\Sales\Total\Quote;

class Discount extends \Magento\Sales\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * Calculate discount tac amount
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @return  Magento_Tax_Model_Sales_Total_Quote
     */
    public function collect(\Magento\Sales\Model\Quote\Address $address)
    {
//        echo 'discount';
    }
}

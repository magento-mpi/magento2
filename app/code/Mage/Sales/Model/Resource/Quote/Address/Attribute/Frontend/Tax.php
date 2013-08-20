<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Quote address attribute frontend tax resource model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Quote_Address_Attribute_Frontend_Tax
    extends Mage_Sales_Model_Resource_Quote_Address_Attribute_Frontend
{
    /**
     * Fetch totals
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Mage_Sales_Model_Resource_Quote_Address_Attribute_Frontend_Tax
     */
    public function fetchTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getTaxAmount();
        if ($amount != 0) {
            $address->addTotal(array(
                'code'  => 'tax',
                'title' => __('Tax'),
                'value' => $amount
            ));
        }
        return $this;
    }
}

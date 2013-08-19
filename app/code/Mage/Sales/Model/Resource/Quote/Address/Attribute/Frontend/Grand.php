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
 * Quote address attribute frontend grand resource model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Quote_Address_Attribute_Frontend_Grand
    extends Mage_Sales_Model_Resource_Quote_Address_Attribute_Frontend
{
    /**
     * Fetch grand total
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Mage_Sales_Model_Resource_Quote_Address_Attribute_Frontend_Grand
     */
    public function fetchTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $address->addTotal(array(
            'code'  => 'grand_total',
            'title' => __('Grand Total'),
            'value' => $address->getGrandTotal(),
            'area'  => 'footer',
        ));
        return $this;
    }
}

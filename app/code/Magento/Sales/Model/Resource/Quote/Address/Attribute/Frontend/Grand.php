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
 * Quote address attribute frontend grand resource model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Quote_Address_Attribute_Frontend_Grand
    extends Magento_Sales_Model_Resource_Quote_Address_Attribute_Frontend
{
    /**
     * Fetch grand total
     *
     * @param Magento_Sales_Model_Quote_Address $address
     * @return Magento_Sales_Model_Resource_Quote_Address_Attribute_Frontend_Grand
     */
    public function fetchTotals(Magento_Sales_Model_Quote_Address $address)
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

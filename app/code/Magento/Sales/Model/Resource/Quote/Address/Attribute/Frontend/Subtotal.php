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
 * Quote address attribute frontend subtotal resource model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Quote_Address_Attribute_Frontend_Subtotal
    extends Magento_Sales_Model_Resource_Quote_Address_Attribute_Frontend
{
    /**
     * Add total
     *
     * @param Magento_Sales_Model_Quote_Address $address
     * @return Magento_Sales_Model_Resource_Quote_Address_Attribute_Frontend_Subtotal
     */
    public function fetchTotals(Magento_Sales_Model_Quote_Address $address)
    {
        $address->addTotal(array(
            'code'  => 'subtotal',
            'title' => __('Subtotal'),
            'value' => $address->getSubtotal()
        ));

        return $this;
    }
}

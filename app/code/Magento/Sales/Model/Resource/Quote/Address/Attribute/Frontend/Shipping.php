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
 * Quote address attribute frontend shipping resource model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Quote_Address_Attribute_Frontend_Shipping
    extends Magento_Sales_Model_Resource_Quote_Address_Attribute_Frontend
{
    /**
     * Fetch totals
     *
     * @param Magento_Sales_Model_Quote_Address $address
     * @return Magento_Sales_Model_Resource_Quote_Address_Attribute_Frontend_Shipping
     */
    public function fetchTotals(Magento_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getShippingAmount();
        if ($amount != 0) {
            $title = __('Shipping & Handling');
            if ($address->getShippingDescription()) {
                $title .= sprintf(' (%s)', $address->getShippingDescription());  
            }
            $address->addTotal(array(
                'code'  => 'shipping',
                'title' => $title,
                'value' => $address->getShippingAmount()
            ));
        }
        return $this;
    }
}

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
 * Quote address attribute frontend discount resource model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Quote_Address_Attribute_Frontend_Discount
    extends Mage_Sales_Model_Resource_Quote_Address_Attribute_Frontend
{
    /**
     * Fetch discount
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Mage_Sales_Model_Resource_Quote_Address_Attribute_Frontend_Discount
     */
    public function fetchTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getDiscountAmount();
        if ($amount != 0) {
            $title = Mage::helper('Mage_Sales_Helper_Data')->__('Discount');
            if ($address->getQuote()->getCouponCode()) {
                $title .= sprintf(' (%s)', $address->getQuote()->getCouponCode());
            }
            $address->addTotal(array(
                'code'  => 'discount',
                'title' => $title,
                'value' => -$amount
            ));
        }
        return $this;
    }
}

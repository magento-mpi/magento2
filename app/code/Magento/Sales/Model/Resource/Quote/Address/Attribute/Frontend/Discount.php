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
 * Quote address attribute frontend discount resource model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Quote_Address_Attribute_Frontend_Discount
    extends Magento_Sales_Model_Resource_Quote_Address_Attribute_Frontend
{
    /**
     * Fetch discount
     *
     * @param Magento_Sales_Model_Quote_Address $address
     * @return Magento_Sales_Model_Resource_Quote_Address_Attribute_Frontend_Discount
     */
    public function fetchTotals(Magento_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getDiscountAmount();
        if ($amount != 0) {
            $title = __('Discount');
            $couponCode = $address->getQuote()->getCouponCode();
            if (strlen($couponCode)) {
                $title .= sprintf(' (%s)', $couponCode);
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

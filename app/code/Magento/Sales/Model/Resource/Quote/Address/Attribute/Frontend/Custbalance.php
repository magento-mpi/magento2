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
 * Quote address attribute frontend cusbalance resource model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Quote_Address_Attribute_Frontend_Custbalance
    extends Magento_Sales_Model_Resource_Quote_Address_Attribute_Frontend
{
    /**
     * Fetch customer balance
     *
     * @param Magento_Sales_Model_Quote_Address $address
     * @return Magento_Sales_Model_Resource_Quote_Address_Attribute_Frontend_Custbalance
     */
    public function fetchTotals(Magento_Sales_Model_Quote_Address $address)
    {
        $custbalance = $address->getCustbalanceAmount();
        if ($custbalance != 0) {
            $address->addTotal(array(
                'code'  => 'custbalance',
                'title' => __('Store Credit'),
                'value' => -$custbalance
            ));
        }
        return $this;
    }
}

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
 * Msrp items total
 * Collects flag if MSRP price is in use
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Quote_Address_Total_Msrp extends Magento_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Collect information about MSRP price enabled
     *
     * @param   Magento_Sales_Model_Quote_Address $address
     * @return  Magento_Sales_Model_Quote_Address_Total_Msrp
     */
    public function collect(Magento_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
        $quote = $address->getQuote();
        $store = Mage::app()->getStore($quote->getStoreId());

        $items = $this->_getAddressItems($address);
        if (!count($items)) {
            return $this;
        }

        $canApplyMsrp = false;
        foreach ($items as $item) {
            if (!$item->getParentItemId() && Mage::helper('Magento_Catalog_Helper_Data')->canApplyMsrp(
                $item->getProductId(),
                Magento_Catalog_Model_Product_Attribute_Source_Msrp_Type::TYPE_BEFORE_ORDER_CONFIRM,
                true
            )) {
                $canApplyMsrp = true;
                break;
            }
        }

        $address->setCanApplyMsrp($canApplyMsrp);

        return $this;
    }
}

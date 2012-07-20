<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Downloadable Order Item BugsCoverage Source
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Model_System_Config_Source_Orderitemstatus
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage_Sales_Model_Order_Item::STATUS_PENDING,
                'label' => Mage::helper('Mage_Downloadable_Helper_Data')->__('Pending')
            ),
            array(
                'value' => Mage_Sales_Model_Order_Item::STATUS_INVOICED,
                'label' => Mage::helper('Mage_Downloadable_Helper_Data')->__('Invoiced')
            )
        );
    }
}

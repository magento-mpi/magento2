<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Downloadable Order Item Status Source
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Downloadable_Model_System_Config_Source_Orderitemstatus
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Magento_Sales_Model_Order_Item::STATUS_PENDING,
                'label' => __('Pending')
            ),
            array(
                'value' => Magento_Sales_Model_Order_Item::STATUS_INVOICED,
                'label' => __('Invoiced')
            )
        );
    }
}

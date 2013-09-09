<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCard_Model_Source_Status extends Magento_Core_Model_Abstract
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Magento_Sales_Model_Order_Item::STATUS_PENDING,
                'label' => __('Ordered')
            ),
            array(
                'value' => Magento_Sales_Model_Order_Item::STATUS_INVOICED,
                'label' => __('Invoiced')
            )
        );
    }
}

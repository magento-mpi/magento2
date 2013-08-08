<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCard_Model_Source_Status extends Mage_Core_Model_Abstract
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage_Sales_Model_Order_Item::STATUS_PENDING,
                'label' => __('Ordered')
            ),
            array(
                'value' => Mage_Sales_Model_Order_Item::STATUS_INVOICED,
                'label' => __('Invoiced')
            )
        );
    }
}

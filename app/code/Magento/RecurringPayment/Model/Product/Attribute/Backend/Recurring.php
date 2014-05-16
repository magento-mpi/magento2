<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RecurringPayment\Model\Product\Attribute\Backend;

/**
 * Backend for recurring payment parameter
 */
class Recurring extends \Magento\Eav\Model\Entity\Attribute\Backend\Serialized
{
    /**
     * Serialize or remove before saving
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return void
     */
    public function beforeSave($product)
    {
        if ($product->hasIsRecurring()) {
            if ($product->getIsRecurring()) {
                parent::beforeSave($product);
            } else {
                $product->unsRecurringPayment();
            }
        }
    }

    /**
     * Unserialize or remove on failure
     *
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\Object $product
     * @return void
     */
    protected function _unserialize(\Magento\Framework\Object $product)
    {
        if ($product->hasIsRecurring()) {
            if ($product->getIsRecurring()) {
                parent::_unserialize($product);
            } else {
                $product->unsRecurringPayment();
            }
        }
    }
}

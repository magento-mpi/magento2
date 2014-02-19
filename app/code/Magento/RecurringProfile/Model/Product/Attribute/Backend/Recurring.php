<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend for recurring profile parameter
 */
namespace Magento\RecurringProfile\Model\Product\Attribute\Backend;

class Recurring
extends \Magento\Eav\Model\Entity\Attribute\Backend\Serialized
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
            if ($product->getIsRecurring() == '1') {
                parent::beforeSave($product);
            } else {
                $product->unsRecurringProfile();
            }
        }
    }

    /**
     * Unserialize or remove on failure
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return void
     */
    protected function _unserialize(\Magento\Object $product)
    {
        if ($product->hasIsRecurring()) {
            if ($product->$product->getIsRecurring() == '1') {
                parent::_unserialize($product);
            } else {
                $product->unsRecurringProfile();
            }
        }
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend for recurring profile parameter
 */
namespace Magento\Catalog\Model\Product\Attribute\Backend;

class Recurring
extends \Magento\Eav\Model\Entity\Attribute\Backend\Serialized
{
    /**
     * Serialize or remove before saving
     * @param \Magento\Catalog\Model\Product $product
     */
    public function beforeSave($product)
    {
        if ($product->hasIsRecurring()) {
            if ($product->isRecurring()) {
                parent::beforeSave($product);
            } else {
                $product->unsRecurringProfile();
            }
        }
    }

    /**
     * Unserialize or remove on failure
     * @param \Magento\Catalog\Model\Product $product
     */
    protected function _unserialize(\Magento\Object $product)
    {
        if ($product->hasIsRecurring()) {
            if ($product->isRecurring()) {
                parent::_unserialize($product);
            } else {
                $product->unsRecurringProfile();
            }
        }
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Catalog\Model\Resource\Setup */

$attribute = $this->getAttribute(\Magento\Catalog\Model\Product::ENTITY, 'image');

if ($attribute) {
    $this->addAttributeToGroup(
        $attribute['entity_type_id'],
        $this->getAttributeSetId($attribute['entity_type_id'], 'Default'),
        'General',
        $attribute['attribute_id'],
        0
    );

    $this->updateAttribute(
        $attribute['entity_type_id'],
        $attribute['attribute_id'],
        'frontend_input_renderer',
        'Magento\Catalog\Block\Adminhtml\Product\Helper\Form\BaseImage'
    );

    $this->updateAttribute(
        $attribute['entity_type_id'],
        $attribute['attribute_id'],
        'used_in_product_listing',
        '1'
    );
}

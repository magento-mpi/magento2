<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Catalog\Model\Resource\Setup */

$groupName = 'Product Details';
$entityTypeId = $this->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
$attributeSetId = $this->getAttributeSetId($entityTypeId, 'Default');

$attributesOrder = array(
    'giftcard_type' => 31,
    'giftcard_amounts' => 32,
    'allow_open_amount' => 33,
    'open_amount_min' => 34,
    'open_amount_max' => 35
);

foreach ($attributesOrder as $key => $order) {
    $attribute = $this->getAttribute($entityTypeId, $key);
    if ($attribute) {
        $this->addAttributeToGroup($entityTypeId, $attributeSetId, $groupName, $attribute['attribute_id'], $order);
    }
}

$attribute = $this->getAttribute($entityTypeId, 'giftcard_type');
if ($attribute) {
    $this->updateAttribute($entityTypeId, $attribute['attribute_id'], 'is_visible', 1);
}

$attribute = $this->getAttribute($entityTypeId, 'allow_open_amount');
if ($attribute) {
    $this->updateAttribute($entityTypeId, $attribute['attribute_id'], 'is_required', 0);
    $this->updateAttribute(
        $entityTypeId,
        $attribute['attribute_id'],
        'frontend_input_renderer',
        'Magento\GiftCard\Block\Adminhtml\Renderer\OpenAmount'
    );
}

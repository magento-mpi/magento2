<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

$taxClasses = Mage::getResourceModel('\Magento\Tax\Model\Resource\TaxClass\Collection')->toArray();
$taxClass = reset($taxClasses['items']);

return array(
    'type_id' => \Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE,
    'sku' => 'configurable_' . uniqid(),
    'name' => 'Test Configurable ' . uniqid(),
    'description' => 'Test description',
    'short_description' => 'Test short description',
    'status' => \Magento\Catalog\Model\Product\Status::STATUS_ENABLED,
    'visibility' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH,
    'price' => 25.50,
    'tax_class_id' => $taxClass['class_id']
);

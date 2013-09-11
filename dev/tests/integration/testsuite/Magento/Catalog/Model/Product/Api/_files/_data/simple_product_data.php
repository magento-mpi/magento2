<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

/** @var $entityType \Magento\Eav\Model\Entity\Type */
$entityType = Mage::getModel('\Magento\Eav\Model\Entity\Type')->loadByCode('catalog_product');
$taxClasses = Mage::getResourceModel('\Magento\Tax\Model\Resource\TaxClass\Collection')->toArray();
$taxClass = reset($taxClasses['items']);

return array(
    'type_id' => \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
    'attribute_set_id' => $entityType->getDefaultAttributeSetId(),
    'sku' => 'simple' . uniqid(),
    'weight' => 1,
    'status' => \Magento\Catalog\Model\Product\Status::STATUS_ENABLED,
    'visibility' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH,
    'name' => 'Simple Product',
    'description' => 'Simple Description',
    'short_description' => 'Simple Short Description',
    'price' => 99.95,
    'tax_class_id' => $taxClass['class_id'],
);

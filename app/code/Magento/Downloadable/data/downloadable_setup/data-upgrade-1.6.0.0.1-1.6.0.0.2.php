<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer = $this;

$applyTo = explode(',', $installer->getAttribute(\Magento\Catalog\Model\Product::ENTITY, 'group_price', 'apply_to'));
if (!in_array(\Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE, $applyTo)) {
    $applyTo[] = \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE;
    $installer->updateAttribute(
        \Magento\Catalog\Model\Product::ENTITY,
        'group_price',
        'apply_to',
        implode(',', $applyTo)
    );
}

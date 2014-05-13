<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Sales\Model\Resource\Setup */
$installer = $this;

$installer->updateAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'gift_wrapping_available',
    'frontend_class',
    'hidden-for-virtual'
);

$installer->updateAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'gift_wrapping_price',
    'frontend_class',
    'hidden-for-virtual'
);

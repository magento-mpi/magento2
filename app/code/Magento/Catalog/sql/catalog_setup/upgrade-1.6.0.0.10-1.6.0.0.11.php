<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer = $this;

$attributeId = $this->getAttribute('catalog_product', 'group_price', 'attribute_id');
$installer->updateAttribute('catalog_product', $attributeId, array(), null, 5);

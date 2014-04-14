<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

/** @var \Magento\Catalog\Model\Resource\Setup $this */
$this->updateAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'status',
    'source_model',
    'Magento\Catalog\Model\Product\Attribute\Source\Status'
);

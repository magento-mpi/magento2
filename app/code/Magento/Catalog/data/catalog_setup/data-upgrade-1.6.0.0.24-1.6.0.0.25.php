<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

/** @var $this \Magento\Catalog\Model\Resource\Setup */
$this->updateAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'status',
    'source_model',
    'Magento\Catalog\Model\Product\Attribute\Source\Status'
);

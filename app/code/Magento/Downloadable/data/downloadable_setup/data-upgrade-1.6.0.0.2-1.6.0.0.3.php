<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $this \Magento\Catalog\Model\Resource\Setup */
$applyTo = array_merge(
    explode(',', $this->getAttribute(\Magento\Catalog\Model\Product::ENTITY, 'weight', 'apply_to')),
    array(\Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE)
);

$this->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, 'weight', 'apply_to', implode(',', $applyTo));

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Catalog\Model\Resource\Setup */

$this->updateAttribute(
    $this->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY),
    'qty',
    'frontend_class',
    'validate-number'
);

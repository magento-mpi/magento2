<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

$applyTo = array_merge(
    explode(',', $this->getAttribute(\Magento\Catalog\Model\Product::ENTITY, 'weight', 'apply_to')),
    array(\Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE)
);

$this->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, 'weight', 'apply_to', implode(',', $applyTo));

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Catalog\Model\Resource\Setup */
$applyTo = array_merge(
    explode(',', $this->getAttribute(\Magento\Catalog\Model\Product::ENTITY, 'weight', 'apply_to')),
    array(\Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL)
);

$this->updateAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'weight',
    'frontend_input_renderer',
    'Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Weight\Renderer'
);
$this->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, 'weight', 'apply_to', implode(',', $applyTo));

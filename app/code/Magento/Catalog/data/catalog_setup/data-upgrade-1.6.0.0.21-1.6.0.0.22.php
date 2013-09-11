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

$this->updateAttribute(
    $this->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY),
    'weight',
    'backend_model',
    '\Magento\Catalog\Model\Product\Attribute\Backend\Weight'
);
$this->updateAttribute(
    $this->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY),
    'name',
    'frontend_class',
    'validate-length maximum-length-255'
);
$this->updateAttribute(
    $this->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY),
    'sku',
    'frontend_class',
    'validate-length maximum-length-64'
);
$this->updateAttribute(
    $this->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY),
    'qty',
    'frontend_class',
    'validate-number'
);
$this->updateAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'weight',
    'frontend_input_renderer',
    '\Magento\Adminhtml\Block\Catalog\Product\Helper\Form\Weight'
);

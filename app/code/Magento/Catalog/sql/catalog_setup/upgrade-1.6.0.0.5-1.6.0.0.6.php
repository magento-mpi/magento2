<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/** @var $installer \Magento\Catalog\Model\Resource\Setup */

$installer->updateAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'url_key',
    'frontend_label',
    'URL Key'
);

$installer->updateAttribute(
    \Magento\Catalog\Model\Category::ENTITY,
    'url_key',
    'frontend_label',
    'URL Key'
);

$installer->updateAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'options_container',
    'frontend_label',
    'Display Product Options In'
);

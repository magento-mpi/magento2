<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer = $this;

$installer->updateAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'options_container',
    'frontend_label',
    'Display Product Options In'
);

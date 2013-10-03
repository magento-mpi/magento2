<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer = $this;

$installer->updateAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'enable_googlecheckout',
    'frontend_label',
    'Is Product Available for Purchase with Google Checkout'
);

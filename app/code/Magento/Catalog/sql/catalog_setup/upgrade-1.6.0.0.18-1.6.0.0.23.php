<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/** @var $installer \Magento\Catalog\Model\Resource\Setup */

$installer->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, 'image', 'used_in_product_listing', '1');

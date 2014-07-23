<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Catalog\Model\Resource\Setup */
foreach (['status', 'visibility'] as $attributeCode) {
    $this->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, $attributeCode, 'is_required_in_admin_store', '1');
}

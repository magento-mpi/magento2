<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Catalog_Model_Resource_Setup */
$installer = $this;

$applyTo = explode(',', $installer->getAttribute(Magento_Catalog_Model_Product::ENTITY, 'group_price', 'apply_to'));
if (!in_array(Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE, $applyTo)) {
    $applyTo[] = Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE;
    $installer->updateAttribute(Magento_Catalog_Model_Product::ENTITY, 'group_price', 'apply_to', implode(',', $applyTo));
}

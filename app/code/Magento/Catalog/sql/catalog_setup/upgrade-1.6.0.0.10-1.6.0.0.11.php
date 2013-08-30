<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Catalog_Model_Resource_Setup */
$installer  = $this;

$attributeId = $this->getAttribute('catalog_product', 'group_price', 'attribute_id');
$installer->updateAttribute('catalog_product', $attributeId, array(), null, 5);

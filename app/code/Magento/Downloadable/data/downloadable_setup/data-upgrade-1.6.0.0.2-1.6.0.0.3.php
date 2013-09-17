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
    explode(',', $this->getAttribute(Magento_Catalog_Model_Product::ENTITY, 'weight', 'apply_to')),
    array(Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE)
);

$this->updateAttribute(Magento_Catalog_Model_Product::ENTITY, 'weight', 'apply_to', implode(',', $applyTo));

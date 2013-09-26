<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this Magento_Rma_Model_Resource_Setup */
//Product's Attribute is_returnable shouldn't be applied to grouped product
//Because it has no sense
/** @var Magento_Catalog_Model_Resource_Setup $installer */
$installer = $this->getCatalogSetup(array('resourceName' => 'catalog_setup'));

$applyTo = Magento_Catalog_Model_Product_Type::TYPE_SIMPLE . ',' .
    Magento_Catalog_Model_Product_Type::TYPE_CONFIGURABLE . ',' .
    Magento_Catalog_Model_Product_Type::TYPE_BUNDLE;

$installer->updateAttribute(Magento_Catalog_Model_Product::ENTITY, 'is_returnable', 'apply_to', $applyTo);

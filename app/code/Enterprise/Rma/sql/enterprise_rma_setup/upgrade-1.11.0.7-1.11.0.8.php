<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

//Product's Attribute is_returnable shouldn't be applied to grouped product
//Because it has no sense
$installer = Mage::getResourceModel('Mage_Catalog_Model_Resource_Setup', array('resourceName' => 'catalog_setup'));

$applyTo = Mage_Catalog_Model_Product_Type::TYPE_SIMPLE . ',' .
    Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE . ',' .
    Mage_Catalog_Model_Product_Type::TYPE_BUNDLE;

$installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'is_returnable', 'apply_to', $applyTo);

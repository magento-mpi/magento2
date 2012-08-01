<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $catalogInstaller Mage_Catalog_Model_Resource_Setup */
$catalogInstaller = Mage::getResourceModel(
    'Mage_Catalog_Model_Resource_Setup',
    array('resourceName' => 'catalog_setup')
);

$entityTypeId = $catalogInstaller->getEntityTypeId(Mage_Catalog_Model_Product::ENTITY);
$attribute = $catalogInstaller->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'tax_class_id');

$catalogInstaller->addAttributeToSet(
    $entityTypeId,
    $catalogInstaller->getAttributeSetId($entityTypeId, 'Minimal'),
    $catalogInstaller->getGeneralGroupName(),
    $attribute['attribute_id']
);

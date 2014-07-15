<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* Create attribute */
/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Catalog\Model\Resource\Setup',
    array('resourceName' => 'catalog_setup')
);
/** @var $attribute \Magento\Catalog\Model\Resource\Eav\Attribute */
$attribute = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Catalog\Model\Resource\Eav\Attribute'
);
$attributeID = 0;
$attribute->loadByCode($installer->getEntityTypeId('catalog_product'), 'test_configurable');
if ($attribute->getId()) {
    $attributeID = $attribute->getId();
    $attribute->delete();
}

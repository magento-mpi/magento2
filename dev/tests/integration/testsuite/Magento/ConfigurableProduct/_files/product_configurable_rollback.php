<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var $options \Magento\Eav\Model\Resource\Entity\Attribute\Option\Collection */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Eav\Model\Config $eavConfig */
$eavConfig = $objectManager->get('Magento\Eav\Model\Config');
$attribute = $eavConfig->getAttribute('catalog_product', 'test_configurable');

/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer = $objectManager->create(
    'Magento\Catalog\Model\Resource\Setup',
    array('resourceName' => 'catalog_setup')
);

/* Create simple products per each option */
$options = $objectManager->create(
    'Magento\Eav\Model\Resource\Entity\Attribute\Option\Collection'
);
$options->setAttributeFilter($attribute->getId());
$attributeValues = array();
foreach ($options as $option) {
    /** @var $product \Magento\Catalog\Model\Product */
    $product = $objectManager->create('Magento\Catalog\Model\Product');
    $product->load($option->getId() * 10)->delete();
}

/** @var $product \Magento\Catalog\Model\Product */
$product = $objectManager->create('Magento\Catalog\Model\Product');
$product->load(1)->delete();

require __DIR__ . '/configurable_attribute_rollback.php';
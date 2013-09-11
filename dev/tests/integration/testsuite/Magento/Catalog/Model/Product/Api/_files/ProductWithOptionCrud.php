<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

$data = require dirname(__FILE__) . '/ProductAttributeData.php';
// add product attributes via installer
$installer = Mage::getModel('Magento\Catalog\Model\Resource\Setup', array('resourceName' => 'core_setup'));
$installer->addAttribute(
    'catalog_product',
    $data['create_text_installer']['code'],
    $data['create_text_installer']['attributeData']
);
$installer->addAttribute(
    'catalog_product',
    $data['create_select_installer']['code'],
    $data['create_select_installer']['attributeData']
);

//add attributes to default attribute set via installer
$installer->addAttributeToSet('catalog_product', 4, 'Default', $data['create_text_installer']['code']);
$installer->addAttributeToSet('catalog_product', 4, 'Default', $data['create_select_installer']['code']);

$attribute = Mage::getModel('Magento\Eav\Model\Entity\Attribute');
$attribute->loadByCode('catalog_product', $data['create_select_installer']['code']);
$collection = Mage::getResourceModel('Magento\Eav\Model\Resource\Entity\Attribute\Option\Collection')
    ->setAttributeFilter($attribute->getId())
    ->load();
$options = $collection->toOptionArray();
$optionValueInstaller = $options[1]['value'];

//add product attributes via api model
$model = Mage::getModel('Magento\Catalog\Model\Product\Attribute\Api');
$response1 = $model->create($data['create_text_api']);
$response2 = $model->create($data['create_select_api']);

//add options
$model = Mage::getModel('Magento\Catalog\Model\Product\Attribute\Api');
$model->addOption($response2, $data['create_select_api_options'][0]);
$model->addOption($response2, $data['create_select_api_options'][1]);
$options = $model->options($response2);
$optionValueApi = $options[1]['value'];

//add attributes to default attribute set via api model
$model = Mage::getModel('Magento\Catalog\Model\Product\Attribute\Set\Api');
$model->attributeAdd($response1, 4);
$model->attributeAdd($response2, 4);

$attributes = array($response1, $response2);
Mage::register('attributes', $attributes);
Mage::register('optionValueApi', $optionValueApi);
Mage::register('optionValueInstaller', $optionValueInstaller);

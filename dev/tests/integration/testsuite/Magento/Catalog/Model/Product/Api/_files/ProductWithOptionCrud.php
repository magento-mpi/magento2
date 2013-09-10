<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

$data = require dirname(__FILE__) . '/ProductAttributeData.php';
// add product attributes via installer
$installer = Mage::getModel('Magento_Catalog_Model_Resource_Setup', array('resourceName' => 'core_setup'));
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

$attribute = Mage::getModel('Magento_Eav_Model_Entity_Attribute');
$attribute->loadByCode('catalog_product', $data['create_select_installer']['code']);
$collection = Mage::getResourceModel('Magento_Eav_Model_Resource_Entity_Attribute_Option_Collection')
    ->setAttributeFilter($attribute->getId())
    ->load();
$options = $collection->toOptionArray();
$optionValueInstaller = $options[1]['value'];

//add product attributes via api model
$model = Mage::getModel('Magento_Catalog_Model_Product_Attribute_Api');
$response1 = $model->create($data['create_text_api']);
$response2 = $model->create($data['create_select_api']);

//add options
$model = Mage::getModel('Magento_Catalog_Model_Product_Attribute_Api');
$model->addOption($response2, $data['create_select_api_options'][0]);
$model->addOption($response2, $data['create_select_api_options'][1]);
$options = $model->options($response2);
$optionValueApi = $options[1]['value'];

//add attributes to default attribute set via api model
$model = Mage::getModel('Magento_Catalog_Model_Product_Attribute_Set_Api');
$model->attributeAdd($response1, 4);
$model->attributeAdd($response2, 4);

$attributes = array($response1, $response2);
/** @var $objectManager Magento_TestFramework_ObjectManager */
$objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
$objectManager->get('Magento_Core_Model_Registry')->register('attributes', $attributes);
$objectManager->get('Magento_Core_Model_Registry')->register('optionValueApi', $optionValueApi);
$objectManager->get('Magento_Core_Model_Registry')->register('optionValueInstaller', $optionValueInstaller);

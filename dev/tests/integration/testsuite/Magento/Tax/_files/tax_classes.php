<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $objectManager Magento_TestFramework_ObjectManager */
$objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
$customerTaxClass1 = $objectManager->create('Magento_Tax_Model_Class')
    ->setClassName('CustomerTaxClass1')
    ->setClassType(Magento_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER)
    ->save();

$customerTaxClass2 = $objectManager->create('Magento_Tax_Model_Class')
    ->setClassName('CustomerTaxClass2')
    ->setClassType(Magento_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER)
    ->save();

$productTaxClass1 = $objectManager->create('Magento_Tax_Model_Class')
    ->setClassName('ProductTaxClass1')
    ->setClassType(Magento_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT)
    ->save();

$productTaxClass2 = $objectManager->create('Magento_Tax_Model_Class')
    ->setClassName('ProductTaxClass2')
    ->setClassType(Magento_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT)
    ->save();

$taxRate = array(
    'tax_country_id' => 'US',
    'tax_region_id' => '12',
    'tax_postcode' => '*',
    'code' => '*',
    'rate' => '7.5'
);
$rate = $objectManager->create('Magento_Tax_Model_Calculation_Rate')->setData($taxRate)->save();

$objectManager->get('Magento_Core_Model_Registry')->register('_fixture/Magento_Tax_Model_Calculation_Rate', $rate);

$ruleData = array(
    'code' => 'Test Rule',
    'priority' => '0',
    'position' => '0',
    'tax_customer_class' => array($customerTaxClass1->getId(), $customerTaxClass2->getId()),
    'tax_product_class' => array($productTaxClass1->getId(), $productTaxClass2->getId()),
    'tax_rate' => array($rate->getId())
);

$taxRule = $objectManager->create('Magento_Tax_Model_Calculation_Rule')->setData($ruleData)->save();

$objectManager->get('Magento_Core_Model_Registry')->register('_fixture/Magento_Tax_Model_Calculation_Rule', $taxRule);

$ruleData['code'] = 'Test Rule Duplicate';

$objectManager->create('Magento_Tax_Model_Calculation_Rule')->setData($ruleData)->save();

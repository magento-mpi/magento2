<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Tax
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$customerTaxClass1 = Mage::getModel('Mage_Tax_Model_Class')
    ->setClassName('CustomerTaxClass1')
    ->setClassType(Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER)
    ->save();

$customerTaxClass2 = Mage::getModel('Mage_Tax_Model_Class')
    ->setClassName('CustomerTaxClass2')
    ->setClassType(Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER)
    ->save();

$productTaxClass1 = Mage::getModel('Mage_Tax_Model_Class')
    ->setClassName('ProductTaxClass1')
    ->setClassType(Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT)
    ->save();

$productTaxClass2 = Mage::getModel('Mage_Tax_Model_Class')
    ->setClassName('ProductTaxClass2')
    ->setClassType(Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT)
    ->save();

$taxRate = array(
    'tax_country_id' => 'US',
    'tax_region_id' => '12',
    'tax_postcode' => '*',
    'code' => '*',
    'rate' => '7.5'
);
$rate = Mage::getModel('Mage_Tax_Model_Calculation_Rate')->setData($taxRate)->save();

$ruleData = array(
    'code' => 'Test Rule',
    'priority' => '0',
    'position' => '0',
    'tax_customer_class' => array($customerTaxClass1->getId(), $customerTaxClass2->getId()),
    'tax_product_class' => array($productTaxClass1->getId(), $productTaxClass2->getId()),
    'tax_rate' => array($rate->getId())
);

$taxRule = Mage::getModel('Mage_Tax_Model_Calculation_Rule')->setData($ruleData)->save();

Mage::register('_fixture/Mage_Tax_Model_Calculation_Rule', $taxRule);

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

$customerTaxClass1 = Mage::getModel('Magento\Tax\Model\ClassModel')
    ->setClassName('CustomerTaxClass1')
    ->setClassType(\Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER)
    ->save();

$customerTaxClass2 = Mage::getModel('Magento\Tax\Model\ClassModel')
    ->setClassName('CustomerTaxClass2')
    ->setClassType(\Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER)
    ->save();

$productTaxClass1 = Mage::getModel('Magento\Tax\Model\ClassModel')
    ->setClassName('ProductTaxClass1')
    ->setClassType(\Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT)
    ->save();

$productTaxClass2 = Mage::getModel('Magento\Tax\Model\ClassModel')
    ->setClassName('ProductTaxClass2')
    ->setClassType(\Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT)
    ->save();

$taxRate = array(
    'tax_country_id' => 'US',
    'tax_region_id' => '12',
    'tax_postcode' => '*',
    'code' => '*',
    'rate' => '7.5'
);
$rate = Mage::getModel('Magento\Tax\Model\Calculation\Rate')->setData($taxRate)->save();

/** @var $objectManager Magento_TestFramework_ObjectManager */
$objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
$objectManager->get('Magento\Core\Model\Registry')->register('_fixture/\Magento\Tax\Model\Calculation\Rate', $rate);

$ruleData = array(
    'code' => 'Test Rule',
    'priority' => '0',
    'position' => '0',
    'tax_customer_class' => array($customerTaxClass1->getId(), $customerTaxClass2->getId()),
    'tax_product_class' => array($productTaxClass1->getId(), $productTaxClass2->getId()),
    'tax_rate' => array($rate->getId())
);

$taxRule = Mage::getModel('Magento\Tax\Model\Calculation\Rule')->setData($ruleData)->save();

$objectManager->get('Magento\Core\Model\Registry')->register('_fixture/\Magento\Tax\Model\Calculation\Rule', $taxRule);

$ruleData['code'] = 'Test Rule Duplicate';

Mage::getModel('Magento\Tax\Model\Calculation\Rule')->setData($ruleData)->save();

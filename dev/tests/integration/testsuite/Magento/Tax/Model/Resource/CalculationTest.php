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

class Magento_Tax_Model_Resource_CalculationTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test that Tax Rate applied only once
     *
     * @magentoDataFixture Magento/Tax/_files/tax_classes.php
     */
    public function testGetRate()
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        $taxRule = $objectManager->get('Magento_Core_Model_Registry')
            ->registry('_fixture/Magento_Tax_Model_Calculation_Rule');
        $customerTaxClasses = $taxRule->getTaxCustomerClass();
        $productTaxClasses = $taxRule->getTaxProductClass();
        $taxRate =  $objectManager->get('Magento_Core_Model_Registry')
            ->registry('_fixture/Magento_Tax_Model_Calculation_Rate');
        $data = new Magento_Object();
        $data->setData(array(
            'country_id' => 'US',
            'region_id' => '12',
            'postcode' => '5555',
            'customer_class_id' => $customerTaxClasses[0],
            'product_class_id' => $productTaxClasses[0]
        ));
        $taxCalculation = Mage::getResourceSingleton('Magento_Tax_Model_Resource_Calculation');
        $this->assertEquals($taxRate->getRate(), $taxCalculation->getRate($data));
    }
}

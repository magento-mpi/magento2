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

class Mage_Tax_Model_Resource_CalculationTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test that Tax Rate applied only once
     *
     * @magentoDataFixture Mage/Tax/_files/tax_classes.php
     */
    public function testGetRate()
    {
        $taxRule = Mage::registry('_fixture/Mage_Tax_Model_Calculation_Rule');
        $customerTaxClasses = $taxRule->getTaxCustomerClass();
        $productTaxClasses = $taxRule->getTaxProductClass();
        $taxRate =  Mage::registry('_fixture/Mage_Tax_Model_Calculation_Rate');
        $data = new Magento_Object();
        $data->setData(array(
            'country_id' => 'US',
            'region_id' => '12',
            'postcode' => '5555',
            'customer_class_id' => $customerTaxClasses[0],
            'product_class_id' => $productTaxClasses[0]
        ));
        $taxCalculation = Mage::getResourceSingleton('Mage_Tax_Model_Resource_Calculation');
        $this->assertEquals($taxRate->getRate(), $taxCalculation->getRate($data));
    }
}

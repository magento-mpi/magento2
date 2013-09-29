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

namespace Magento\Tax\Model\Resource;

class CalculationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that Tax Rate applied only once
     *
     * @magentoDataFixture Magento/Tax/_files/tax_classes.php
     */
    public function testGetRate()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $taxRule = $objectManager->get('Magento\Core\Model\Registry')
            ->registry('_fixture/Magento_Tax_Model_Calculation_Rule');
        $customerTaxClasses = $taxRule->getTaxCustomerClass();
        $productTaxClasses = $taxRule->getTaxProductClass();
        $taxRate =  $objectManager->get('Magento\Core\Model\Registry')
            ->registry('_fixture/Magento_Tax_Model_Calculation_Rate');
        $data = new \Magento\Object();
        $data->setData(array(
            'country_id' => 'US',
            'region_id' => '12',
            'postcode' => '5555',
            'customer_class_id' => $customerTaxClasses[0],
            'product_class_id' => $productTaxClasses[0]
        ));
        $taxCalculation = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Tax\Model\Resource\Calculation');
        $this->assertEquals($taxRate->getRate(), $taxCalculation->getRate($data));
    }
}

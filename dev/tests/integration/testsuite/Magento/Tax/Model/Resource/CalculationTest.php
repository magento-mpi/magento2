<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tax\Model\Resource;

class CalculationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that Tax Rate applied only once
     *
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Tax/_files/tax_classes.php
     */
    public function testGetRate()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $taxRule = $objectManager->get('Magento\Framework\Registry')
            ->registry('_fixture/Magento_Tax_Model_Calculation_Rule');
        $customerTaxClasses = $taxRule->getCustomerTaxClassIds();
        $productTaxClasses = $taxRule->getProductTaxClassIds();
        $taxRate = $objectManager->get('Magento\Framework\Registry')
            ->registry('_fixture/Magento_Tax_Model_Calculation_Rate');
        $data = new \Magento\Framework\Object();
        $data->setData(
            [
                'tax_country_id' => 'US',
                'taxregion_id' => '12',
                'tax_postcode' => '5555',
                'customer_class_id' => $customerTaxClasses[0],
                'product_class_id' => $productTaxClasses[0],
            ]
        );
        $taxCalculation = $objectManager->get('Magento\Tax\Model\Resource\Calculation');
        $this->assertEquals($taxRate->getRateIds(), $taxCalculation->getRate($data));
    }
}

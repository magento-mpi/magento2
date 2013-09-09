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

class Magento_Tax_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoConfigFixture default_store tax/classes/default_customer_tax_class 1
     */
    public function testGetDefaultCustomerTaxClass()
    {
        /** @var $helper Magento_Tax_Helper_Data */
        $helper = Magento_Test_Helper_Bootstrap::getObjectManager()->get('Magento_Tax_Helper_Data');
        $this->assertEquals(1, $helper->getDefaultCustomerTaxClass());
    }

    /**
     * @magentoConfigFixture default_store tax/classes/default_product_tax_class 1
     */
    public function testGetDefaultProductTaxClass()
    {
        /** @var $helper Magento_Tax_Helper_Data */
        $helper = Magento_Test_Helper_Bootstrap::getObjectManager()->get('Magento_Tax_Helper_Data');
        $this->assertEquals(1, $helper->getDefaultProductTaxClass());
    }

}

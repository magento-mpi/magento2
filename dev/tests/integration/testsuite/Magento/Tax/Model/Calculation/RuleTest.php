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


/**
 * @magentoDataFixture Magento/Tax/_files/tax_classes.php
 */
class Magento_Tax_Model_Calculation_RuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test that first value in multiselect applied as default if there is no default value in config
     *
     * @magentoConfigFixture default_store tax/classes/default_customer_tax_class 0
     */
    public function testGetCustomerTaxClassWithDefaultFirstValue()
    {
        $model = new Magento_Tax_Model_Calculation_Rule(
            Mage::getModel('Magento_Core_Model_Context'),
            Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Registry'),
            Mage::helper('Magento_Tax_Helper_Data'),
            $this->_getTaxClassMock(
                'getCustomerClasses',
                Magento_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER
            ),
            null,
            null,
            array()
        );
        $this->assertEquals(1, $model->getCustomerTaxClassWithDefault());
    }

    /**
     * Test that default value for multiselect is retrieve from config
     *
     * @magentoConfigFixture default_store tax/classes/default_customer_tax_class 2
     */
    public function testGetCustomerTaxClassWithDefaultFromConfig()
    {
        $model = new Magento_Tax_Model_Calculation_Rule(
            Mage::getModel('Magento_Core_Model_Context'),
            Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Registry'),
            Mage::helper('Magento_Tax_Helper_Data'),
            $this->_getTaxClassMock(
                'getCustomerClasses',
                Magento_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER
            ),
            null,
            null,
            array()
        );
        $this->assertEquals(2, $model->getCustomerTaxClassWithDefault());
    }

    /**
     * Test that first value in multiselect applied as default if there is no default value in config
     *
     * @magentoConfigFixture default_store tax/classes/default_product_tax_class 0
     */
    public function testGetProductTaxClassWithDefaultFirstValue()
    {
        $model = new Magento_Tax_Model_Calculation_Rule(
            Mage::getModel('Magento_Core_Model_Context'),
            Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Registry'),
            Mage::helper('Magento_Tax_Helper_Data'),
            $this->_getTaxClassMock(
                'getProductClasses',
                 Magento_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT
            ),
            null,
            null,
            array()
        );
        $this->assertEquals(1, $model->getProductTaxClassWithDefault());
    }

    /**
     * Test that default value for multiselect is retrieve from config
     *
     * @magentoConfigFixture default_store tax/classes/default_product_tax_class 2
     */
    public function testGetProductTaxClassWithDefaultFromConfig()
    {
        $model = new Magento_Tax_Model_Calculation_Rule(
            Mage::getModel('Magento_Core_Model_Context'),
            Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Registry'),
            Mage::helper('Magento_Tax_Helper_Data'),
            $this->_getTaxClassMock(
                'getProductClasses',
                 Magento_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT
            ),
            null,
            null,
            array()
        );
        $this->assertEquals(2, $model->getProductTaxClassWithDefault());
    }

    /**
     * Test get all options
     *
     * @dataProvider getAllOptionsProvider
     */
    public function testGetAllOptions($classFilter, $expected)
    {
        $model = new Magento_Tax_Model_Calculation_Rule(
            Mage::getModel('Magento_Core_Model_Context'),
            Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Registry'),
            Mage::helper('Magento_Tax_Helper_Data'),
            Mage::getModel('Magento_Tax_Model_Class'),
            null,
            null,
            array()
        );
        $classes = $model->getAllOptionsForClass($classFilter);
        $this->assertCount(count($expected), $classes);
        $count = 0;
        foreach ($classes as $class) {
            $this->assertEquals($expected[$count], $class['label']);
            $count++;
        }
    }

    /**
     * Data provider for testGetAllOptions() method
     */
    public function getAllOptionsProvider()
    {
        return array(
            array(
                Magento_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER,
                array('Retail Customer', 'CustomerTaxClass1', 'CustomerTaxClass2')
            ),
            array(
                Magento_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT,
                array('Taxable Goods', 'ProductTaxClass1', 'ProductTaxClass2')
            ),
        );
    }

    /**
     * Get Product|Customer tax class mock
     *
     * @param string $callback
     * @param string $filter
     * @return Magento_Tax_Model_Class
     */
    protected function _getTaxClassMock($callback, $filter)
    {
        $collection = $this->getMock(
            'Magento_Tax_Model_Resource_Class_Collection',
            array('setClassTypeFilter', 'toOptionArray'),
            array(), '', false
        );
        $collection->expects($this->any())
            ->method('setClassTypeFilter')
            ->with($filter)
            ->will($this->returnValue($collection));

        $collection->expects($this->any())
            ->method('toOptionArray')
            ->will($this->returnCallback(array($this, $callback)));

        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $mock = $this->getMock(
            'Magento_Tax_Model_Class',
            array('getCollection'),
            array(
                Mage::getModel('Magento_Core_Model_Context'),
                $objectManager->get('Magento_Core_Model_Registry'),
                Mage::getModel('Magento_Tax_Model_Class_Factory'),
            ),
            '',
            true
        );
        $mock->expects($this->any())
            ->method('getCollection')
            ->will($this->returnValue($collection));

        return $mock;
    }

    /**
     * Prepare Customer Tax Classes
     * @return array
     */
    public function getCustomerClasses()
    {
        return array(
            array(
                'value' => '1',
                'name' => 'Retail Customer'
            ),
            array(
                'value' => '2',
                'name' => 'Guest'
            )
        );
    }

    /**
     * Prepare Product Tax classes
     * @return array
     */
    public function getProductClasses()
    {
        return array(
            array(
                'value' => '1',
                'name' => 'Taxable Goods'
            ),
            array(
                'value' => '2',
                'name' => 'Shipping'
            )
        );
    }
}

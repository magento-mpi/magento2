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
 * @magentoDataFixture Mage/Tax/_files/tax_classes.php
 */
class Mage_Tax_Model_Calculation_RuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * Customer Tax Model Class Mock
     *
     * @var Mage_Tax_Model_Class
     */
    protected $_customerTaxClassMock;

    /**
     * Product Tax Model Class Mock
     *
     * @var Mage_Tax_Model_Class
     */
    protected $_productTaxClassMock;


    protected function setUp()
    {
        $this->_customerTaxClassMock = $this->_getTaxClassMock(
            'getCustomerClasses',
            Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER
        );

        $this->_productTaxClassMock = $this->_getTaxClassMock(
            'getProductClasses',
             Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT
        );
    }

    /**
     * Test that first value in multiselect applied as default if there is no default value in config
     *
     * @magentoConfigFixture default_store tax/classes/default_customer_tax_class 0
     */
    public function testGetCustomerTaxClassWithDefaultFirstValue()
    {
        $model = new Mage_Tax_Model_Calculation_Rule(array('tax_model_class' => $this->_customerTaxClassMock));
        $this->assertEquals(1, $model->getCustomerTaxClassWithDefault());
    }

    /**
     * Test that default value for multiselect is retrieve from config
     *
     * @magentoConfigFixture default_store tax/classes/default_customer_tax_class 2
     */
    public function testGetCustomerTaxClassWithDefaultFromConfig()
    {
        $model = new Mage_Tax_Model_Calculation_Rule(array('tax_model_class' => $this->_customerTaxClassMock));
        $this->assertEquals(2, $model->getCustomerTaxClassWithDefault());
    }

    /**
     * Test that first value in multiselect applied as default if there is no default value in config
     *
     * @magentoConfigFixture default_store tax/classes/default_product_tax_class 0
     */
    public function testGetProductTaxClassWithDefaultFirstValue()
    {
        $model = new Mage_Tax_Model_Calculation_Rule(array('tax_model_class' => $this->_productTaxClassMock));
        $this->assertEquals(1, $model->getProductTaxClassWithDefault());
    }

    /**
     * Test that default value for multiselect is retrieve from config
     *
     * @magentoConfigFixture default_store tax/classes/default_product_tax_class 2
     */
    public function testGetProductTaxClassWithDefaultFromConfig()
    {
        $model = new Mage_Tax_Model_Calculation_Rule(array('tax_model_class' => $this->_productTaxClassMock));
        $this->assertEquals(2, $model->getProductTaxClassWithDefault());
    }

    /**
     * Test get all options
     *
     * @dataProvider getAllOptionsProvider
     */
    public function testGetAllOptions($classFilter, $expected)
    {
        $model = new Mage_Tax_Model_Calculation_Rule();
        $classes = $model->getAllOptionsForClass($classFilter);
        $this->assertCount(count($expected), $classes);
        $i = 0;
        foreach($classes as $class) {
            $this->assertEquals($expected[$i], $class['label']);
            $i++;
        }
    }

    /**
     * Data provider for testGetAllOptions() method
     */
    public function getAllOptionsProvider()
    {
        return array(
            array(
                Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER,
                array('Retail Customer', 'CustomerTaxClass1', 'CustomerTaxClass2')
            ),
            array(
                Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT,
                array('Taxable Goods', 'Shipping', 'ProductTaxClass1', 'ProductTaxClass2')
            ),
        );
    }

    /**
     * Get Product|Customer tax class mock
     *
     * @param string $callback
     * @param string $filter
     * @return Mage_Tax_Model_Class
     */
    protected function _getTaxClassMock($callback, $filter)
    {
        $collection = $this->getMock(
            'Mage_Tax_Model_Resource_Class_Collection',
            array('setClassTypeFilter', 'toOptionArray')
        );
        $collection->expects($this->any())
            ->method('setClassTypeFilter')
            ->with($filter)
            ->will($this->returnValue($collection));

        $collection->expects($this->any())
            ->method('toOptionArray')
            ->will($this->returnCallback(array($this, $callback)));

        $mock = $this->getMock(
            'Mage_Tax_Model_Class',
            array('getCollection')
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
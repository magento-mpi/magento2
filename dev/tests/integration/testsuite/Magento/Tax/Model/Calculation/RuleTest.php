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
namespace Magento\Tax\Model\Calculation;

/**
 * @magentoDataFixture Magento/Tax/_files/tax_classes.php
 */
class RuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
    }

    /**
     * Test that first value in multiselect applied as default if there is no default value in config
     *
     * @magentoConfigFixture default_store tax/classes/default_customer_tax_class 0
     */
    public function testGetCustomerTaxClassWithDefaultFirstValue()
    {
        $taxClass = $this->_getTaxClassMock(
            'getCustomerClasses',
            \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER
        );
        $model = $this->_objectManager->create(
            'Magento\Tax\Model\Calculation\Rule',
            array('taxClass' => $taxClass, 'registry' => $this->_getRegistryClassMock())
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
        $taxClass = $this->_getTaxClassMock(
            'getCustomerClasses',
            \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER
        );
        $model = $this->_objectManager->create(
            'Magento\Tax\Model\Calculation\Rule',
            array('taxClass' => $taxClass, 'registry' => $this->_getRegistryClassMock())
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
        $taxClass = $this->_getTaxClassMock(
            'getProductClasses',
            \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT
        );
        $model = $this->_objectManager->create(
            'Magento\Tax\Model\Calculation\Rule',
            array('taxClass' => $taxClass, 'registry' => $this->_getRegistryClassMock())
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
        $taxClass = $this->_getTaxClassMock(
            'getProductClasses',
            \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT
        );
        $model = $this->_objectManager->create(
            'Magento\Tax\Model\Calculation\Rule',
            array('taxClass' => $taxClass, 'registry' => $this->_getRegistryClassMock())
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
        $model = $this->_objectManager->create(
            'Magento\Tax\Model\Calculation\Rule',
            array('registry' => $this->_getRegistryClassMock())
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
                \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER,
                array('Retail Customer', 'CustomerTaxClass1', 'CustomerTaxClass2')
            ),
            array(
                \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT,
                array('Taxable Goods', 'ProductTaxClass1', 'ProductTaxClass2')
            )
        );
    }

    /**
     * @return \Magento\Framework\Registry
     */
    protected function _getRegistryClassMock()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        return $objectManager->get('Magento\Framework\Registry');
    }

    /**
     * Get Product|Customer tax class mock
     *
     * @param string $callback
     * @param string $filter
     * @return \Magento\Tax\Model\ClassModel
     */
    protected function _getTaxClassMock($callback, $filter)
    {
        $collection = $this->getMock(
            'Magento\Tax\Model\Resource\TaxClass\Collection',
            array('setClassTypeFilter', 'toOptionArray'),
            array(),
            '',
            false
        );
        $collection->expects(
            $this->any()
        )->method(
            'setClassTypeFilter'
        )->with(
            $filter
        )->will(
            $this->returnValue($collection)
        );

        $collection->expects(
            $this->any()
        )->method(
            'toOptionArray'
        )->will(
            $this->returnCallback(array($this, $callback))
        );

        $mock = $this->getMock(
            'Magento\Tax\Model\ClassModel',
            array('getCollection'),
            array(
                $this->_objectManager->create('Magento\Framework\Model\Context'),
                $this->_objectManager->get('Magento\Framework\Registry'),
                $this->_objectManager->get('Magento\Tax\Model\TaxClass\Factory')
            ),
            '',
            true
        );
        $mock->expects($this->any())->method('getCollection')->will($this->returnValue($collection));

        return $mock;
    }

    /**
     * Prepare Customer Tax Classes
     * @return array
     */
    public function getCustomerClasses()
    {
        return array(array('value' => '1', 'name' => 'Retail Customer'), array('value' => '2', 'name' => 'Guest'));
    }

    /**
     * Prepare Product Tax classes
     * @return array
     */
    public function getProductClasses()
    {
        return array(array('value' => '1', 'name' => 'Taxable Goods'), array('value' => '2', 'name' => 'Shipping'));
    }
}

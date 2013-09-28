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
     * Test that first value in multiselect applied as default if there is no default value in config
     *
     * @magentoConfigFixture default_store tax/classes/default_customer_tax_class 0
     */
    public function testGetCustomerTaxClassWithDefaultFirstValue()
    {
        $model = new \Magento\Tax\Model\Calculation\Rule(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Event\Manager'),
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Context'),
            $this->_getRegistryClassMock(),
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Tax\Helper\Data'),
            $this->_getTaxClassMock(
                'getCustomerClasses',
                \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER
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
        $model = new \Magento\Tax\Model\Calculation\Rule(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Event\Manager'),
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Context'),
            $this->_getRegistryClassMock(),
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Tax\Helper\Data'),
            $this->_getTaxClassMock(
                'getCustomerClasses',
                \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER
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
        $model = new \Magento\Tax\Model\Calculation\Rule(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Event\Manager'),
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Context'),
            $this->_getRegistryClassMock(),
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Tax\Helper\Data'),
            $this->_getTaxClassMock(
                'getProductClasses',
                 \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT
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
        $model = new \Magento\Tax\Model\Calculation\Rule(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Event\Manager'),
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Context'),
            $this->_getRegistryClassMock(),
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Tax\Helper\Data'),
            $this->_getTaxClassMock(
                'getProductClasses',
                 \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT
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
        $model = new \Magento\Tax\Model\Calculation\Rule(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Event\Manager'),
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Context'),
            $this->_getRegistryClassMock(),
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Tax\Helper\Data'),
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Tax\Model\ClassModel'),
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
                \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER,
                array('Retail Customer', 'CustomerTaxClass1', 'CustomerTaxClass2')
            ),
            array(
                \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT,
                array('Taxable Goods', 'ProductTaxClass1', 'ProductTaxClass2')
            ),
        );
    }

    /**
     * @return \Magento\Core\Model\Registry
     */
    protected function _getRegistryClassMock()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        return $objectManager->get('Magento\Core\Model\Registry');
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
            'Magento\Tax\Model\Resource\ClassResource\Collection',
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

        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $mock = $this->getMock(
            'Magento\Tax\Model\ClassModel',
            array('getCollection'),
            array(
                \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Context'),
                $objectManager->get('Magento\Core\Model\Registry'),
                \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Tax\Model\ClassModel\Factory'),
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

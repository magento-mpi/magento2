<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Block\Adminhtml\Rule\Edit;

class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /** @var \Magento\Tax\Block\Adminhtml\Rule\Edit\Form */
    protected $_block;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $customerTaxClassSourceMock = $this->getMockBuilder('Magento\Tax\Model\TaxClass\Source\Customer')
            ->setMethods(['getAllOptions'])
            ->disableOriginalConstructor()
            ->getMock();
        $customerTaxClassSourceMock->expects($this->any())
            ->method('getAllOptions')
            ->will(
                $this->returnValue(
                    [['value' => '1', 'name' => 'Retail Customer'], ['value' => '2', 'name' => 'Guest']]
                )
            );
        $productTaxClassSourceMock = $this->getMockBuilder('Magento\Tax\Model\TaxClass\Source\Product')
            ->setMethods(['getAllOptions'])
            ->disableOriginalConstructor()
            ->getMock();
        $productTaxClassSourceMock->expects($this->any())
            ->method('getAllOptions')
            ->will(
                $this->returnValue(
                    [['value' => '1', 'name' => 'Taxable Goods'], ['value' => '2', 'name' => 'Shipping']]
                )
            );
        $this->_block = $this->_objectManager->create(
            'Magento\Tax\Block\Adminhtml\Rule\Edit\Form',
            [
                'registry' => $this->_objectManager->get('Magento\Framework\Registry'),
                'customerTaxClassSource' => $customerTaxClassSourceMock,
                'productTaxClassSource' => $productTaxClassSourceMock
            ]
        );
    }

    /**
     * Test that first value in multiselect applied as default if there is no default value in config
     *
     * @magentoConfigFixture default_store tax/classes/default_customer_tax_class 0
     */
    public function testGetCustomerTaxClassWithDefaultFirstValue()
    {
        $this->assertEquals(1, $this->_block->getDefaultCustomerTaxClass());
    }

    /**
     * Test that default value for multiselect is retrieve from config
     *
     * @magentoConfigFixture default_store tax/classes/default_customer_tax_class 2
     */
    public function testGetCustomerTaxClassWithDefaultFromConfig()
    {
        $this->assertEquals(2, $this->_block->getDefaultCustomerTaxClass());
    }

    /**
     * Test that first value in multiselect applied as default if there is no default value in config
     *
     * @magentoConfigFixture default_store tax/classes/default_product_tax_class 0
     */
    public function testGetProductTaxClassWithDefaultFirstValue()
    {
        $this->assertEquals(1, $this->_block->getDefaultProductTaxClass());
    }

    /**
     * Test that default value for multiselect is retrieve from config
     *
     * @magentoConfigFixture default_store tax/classes/default_product_tax_class 2
     */
    public function testGetProductTaxClassWithDefaultFromConfig()
    {
        $this->assertEquals(2, $this->_block->getDefaultProductTaxClass());
    }
}

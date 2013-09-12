<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Sales_Model_Quote_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Sales_Model_Quote_Config
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_attributeConfig;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManager;

    protected function setUp()
    {
        $this->_attributeConfig = $this->getMock('Magento_Catalog_Model_Attribute_Config', array(), array(), '', false);
        $this->_eventManager = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false);
        $this->_model = new Magento_Sales_Model_Quote_Config($this->_attributeConfig, $this->_eventManager);
    }

    public function testGetProductAttributes()
    {
        $attributes = array('attribute_one', 'attribute_two');
        $this->_attributeConfig
            ->expects($this->once())
            ->method('getAttributeNames')
            ->with('sales_quote_item')
            ->will($this->returnValue($attributes))
        ;
        $constraint = function ($actual) {
            try {
                $expectedData = array('attribute_one' => true, 'attribute_two' => true);
                PHPUnit_Framework_Assert::assertArrayHasKey('attributes', $actual);
                PHPUnit_Framework_Assert::assertInstanceOf('Magento_Object', $actual['attributes']);
                PHPUnit_Framework_Assert::assertEquals($expectedData, $actual['attributes']->getData());
                return true;
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                return false;
            }
        };
        $this->_eventManager
            ->expects($this->once())
            ->method('dispatch')
            ->with('sales_quote_config_get_product_attributes', $this->callback($constraint))
        ;
        $this->assertEquals($attributes, $this->_model->getProductAttributes());
    }
}

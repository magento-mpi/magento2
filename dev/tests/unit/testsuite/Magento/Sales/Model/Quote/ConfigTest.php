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

    protected function setUp()
    {
        $this->_attributeConfig = $this->getMock('Magento_Catalog_Model_Attribute_Config', array(), array(), '', false);
        $this->_model = new Magento_Sales_Model_Quote_Config($this->_attributeConfig);
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
        $this->assertEquals($attributes, $this->_model->getProductAttributes());
    }
}

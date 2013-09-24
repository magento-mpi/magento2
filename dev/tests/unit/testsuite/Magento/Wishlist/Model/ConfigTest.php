<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Wishlist_Model_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Wishlist_Model_Config
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeConfig;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_catalogConfig;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_attributeConfig;

    protected function setUp()
    {
        $this->_storeConfig = $this->getMock('Magento_Core_Model_Store_ConfigInterface');
        $this->_catalogConfig = $this->getMock('Magento_Catalog_Model_Config', array(), array(), '', false);
        $this->_attributeConfig = $this->getMock('Magento_Catalog_Model_Attribute_Config', array(), array(), '', false);
        $this->_model = new Magento_Wishlist_Model_Config(
            $this->_storeConfig, $this->_catalogConfig, $this->_attributeConfig
        );
    }

    public function testGetProductAttributes()
    {
        $this->_catalogConfig
            ->expects($this->once())
            ->method('getProductAttributes')
            ->will($this->returnValue(array('attribute_one', 'attribute_two')))
        ;
        $this->_attributeConfig
            ->expects($this->once())
            ->method('getAttributeNames')
            ->with('wishlist_item')
            ->will($this->returnValue(array('attribute_three')))
        ;
        $expectedResult = array('attribute_one', 'attribute_two', 'attribute_three');
        $this->assertEquals($expectedResult, $this->_model->getProductAttributes());
    }
}

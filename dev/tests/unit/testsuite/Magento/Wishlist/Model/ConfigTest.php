<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Wishlist\Model;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Wishlist\Model\Config
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_catalogConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_attributeConfig;

    protected function setUp()
    {
        $this->_storeConfig = $this->getMock('Magento\Core\Model\Store\ConfigInterface');
        $this->_catalogConfig = $this->getMock('Magento\Catalog\Model\Config', array(), array(), '', false);
        $this->_attributeConfig = $this->getMock('Magento\Catalog\Model\Attribute\Config', array(), array(), '', false);
        $this->_model = new \Magento\Wishlist\Model\Config(
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

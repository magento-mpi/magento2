<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Quote;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Quote\Config
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_attributeConfig;

    protected function setUp()
    {
        $this->_attributeConfig = $this->getMock(
            'Magento\Catalog\Model\Attribute\Config',
            array(),
            array(),
            '',
            false
        );
        $this->_model = new \Magento\Sales\Model\Quote\Config($this->_attributeConfig);
    }

    public function testGetProductAttributes()
    {
        $attributes = array('attribute_one', 'attribute_two');
        $this->_attributeConfig->expects(
            $this->once()
        )->method(
            'getAttributeNames'
        )->with(
            'sales_quote_item'
        )->will(
            $this->returnValue($attributes)
        );
        $this->assertEquals($attributes, $this->_model->getProductAttributes());
    }
}

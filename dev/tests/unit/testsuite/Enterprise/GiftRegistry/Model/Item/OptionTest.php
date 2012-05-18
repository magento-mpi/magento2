<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Bundle
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftRegistry_Model_Item_OptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_GiftRegistry_Model_Item_Option|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = $this->getMock('Enterprise_GiftRegistry_Model_Item_Option',
            array('getValue'), array(), '', false);
    }

    /**
     * @param mixed $product
     * @param mixed $expectedProduct
     * @param int $expectedProductId
     * @dataProvider setProductDataProvider
     */
    public function testSetProduct($product, $expectedProduct, $expectedProductId)
    {
        $this->_model = $this->getMock('Enterprise_GiftRegistry_Model_Item_Option',
            array('getValue'), array(), '', false);
        $this->_model->setProduct($product);

        $this->assertEquals($expectedProduct, $this->_model->getProduct());
        $this->assertEquals($expectedProductId, $this->_model->getProductId());
        unset($this->_model);
    }

    public function setProductDataProvider()
    {
        $product = $this->getMock('Mage_Catalog_Model_Product', array('getId'), array(), '', false);
        $product->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(3));
        return array(
            array($product, $product, 3),
            array(null, null, null),
        );
    }
}

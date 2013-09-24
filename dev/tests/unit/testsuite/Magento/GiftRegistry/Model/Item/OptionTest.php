<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Model\Item;

class OptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param mixed $product
     * @param mixed $expectedProduct
     * @param int $expectedProductId
     * @dataProvider setProductDataProvider
     */
    public function testSetProduct($product, $expectedProduct, $expectedProductId)
    {
        $model = $this->getMock('Magento\GiftRegistry\Model\Item\Option',
            array('getValue'), array(), '', false);
        $model->setProduct($product);

        $this->assertEquals($expectedProduct, $model->getProduct());
        $this->assertEquals($expectedProductId, $model->getProductId());
    }

    public function setProductDataProvider()
    {
        $product = $this->getMock('Magento\Catalog\Model\Product', array('getId'), array(), '', false);
        $product->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(3));
        return array(
            array($product, $product, 3),
            array(null, null, null),
        );
    }
}

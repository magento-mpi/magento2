<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Quote\Item;

class RelatedProductsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Quote\Item\RelatedProducts
     */
    protected $model;

    /**
     * @var array
     */
    protected $relatedProductTypes;

    protected function setUp()
    {
        $this->relatedProductTypes = array('type1', 'type2', 'type3');
        $this->model = new \Magento\Sales\Model\Quote\Item\RelatedProducts($this->relatedProductTypes);
    }

    /**
     * @param string $optionValue
     * @param int|bool $productId
     * @param array $expectedResult
     *
     * @covers \Magento\Sales\Model\Quote\Item\RelatedProducts::getRelatedProductIds
     * @dataProvider getRelatedProductIdsDataProvider
     */
    public function testGetRelatedProductIds($optionValue, $productId, $expectedResult)
    {
        $quoteItemMock = $this->getMock('\Magento\Sales\Model\Quote\Item', array(), array(), '', false);
        $itemOptionMock = $this->getMock(
            '\Magento\Sales\Model\Quote\Item\Option', array('getValue', 'getProductId', '__wakeup'), array(), '', false
        );

        $quoteItemMock->expects($this->once())
            ->method('getOptionByCode')
            ->with('product_type')
            ->will($this->returnValue($itemOptionMock));

        $itemOptionMock->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($optionValue));

        $itemOptionMock->expects($this->any())
            ->method('getProductId')
            ->will($this->returnValue($productId));

        $this->assertEquals($expectedResult, $this->model->getRelatedProductIds(array($quoteItemMock)));
    }

    /*
     * Data provider for testGetRelatedProductIds
     *
     * @return array
     */
    public function getRelatedProductIdsDataProvider()
    {
        return array(
            'case1' => array(
                'optionValue' => 'type1',
                'productId' => 123,
                'expectedResult' => array(123)
            ),
            'case2' => array(
                'optionValue' => 'other_type',
                'productId' => 123,
                'expectedResult' => array()
            ),
            'case3' => array(
                'optionValue' => 'type1',
                'productId' => false,
                'expectedResult' => array()
            ),
            'case4' => array(
                'optionValue' => 'other_type',
                'productId' => false,
                'expectedResult' => array()
            )
        );
    }

    /**
     * @covers \Magento\Sales\Model\Quote\Item\RelatedProducts::getRelatedProductIds
     */
    public function testGetRelatedProductIdsNoOptions()
    {
        $quoteItemMock = $this->getMock('\Magento\Sales\Model\Quote\Item', array(), array(), '', false);

        $quoteItemMock->expects($this->once())
            ->method('getOptionByCode')
            ->with('product_type')
            ->will($this->returnValue(new \stdClass()));

        $this->assertEquals(array(), $this->model->getRelatedProductIds(array($quoteItemMock)));
    }
}

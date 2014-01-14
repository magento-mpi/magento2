<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Helper\Product\Configuration\Plugin;

class GroupedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GroupedProduct\Helper\Product\Configuration\Plugin\Grouped
     */
    protected $groupedConfigPlugin;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $invocationChainMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $typeInstanceMock;

    /**
     * @var array
     */
    protected $inputArguments;

    protected function setUp()
    {
        $this->groupedConfigPlugin = new Grouped();

        $this->invocationChainMock = $this->getMock('Magento\Code\Plugin\InvocationChain', array(), array(), '', false);
        $this->itemMock = $this->getMock('Magento\Catalog\Model\Product\Configuration\Item\ItemInterface');
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $this->typeInstanceMock = $this->getMock(
            'Magento\GroupedProduct\Model\Product\Type\Grouped', array(), array(), '', false
        );

        $this->itemMock->expects($this->any())
            ->method('getProduct')
            ->will($this->returnValue($this->productMock));

        $this->productMock->expects($this->any())
            ->method('getTypeInstance')
            ->will($this->returnValue($this->typeInstanceMock));

        $this->inputArguments = array('item' => $this->itemMock);
    }

    /**
     * @covers Magento\GroupedProduct\Helper\Product\Configuration\Plugin\Grouped::aroundGetOptions
     */
    public function testAroundGetOptionsGroupedProductWithAssociated()
    {
        $associatedProductId = 'associatedId';
        $associatedProdName = 'associatedProductName';

        $associatedProdMock = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);

        $associatedProdMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($associatedProductId));

        $associatedProdMock->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($associatedProdName));

        $this->typeInstanceMock->expects($this->once())
            ->method('getAssociatedProducts')
            ->with($this->productMock)
            ->will($this->returnValue(array($associatedProdMock)));

        $this->productMock->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue(\Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE));

        $quantityItemMock = $this->getMock(
            'Magento\Catalog\Model\Product\Configuration\Item\ItemInterface', array('getValue', 'getProduct', 'getOptionByCode', 'getFileDownloadParams')
        );

        $quantityItemMock->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue(1));

        $this->itemMock->expects($this->once())
            ->method('getOptionByCode')
            ->with('associated_product_' . $associatedProductId)
            ->will($this->returnValue($quantityItemMock));

        $this->invocationChainMock->expects($this->once())
            ->method('proceed')
            ->with($this->inputArguments)
            ->will($this->returnValue(array(array('label' => 'productName', 'value' => 2))));

        $result = $this->groupedConfigPlugin->aroundGetOptions($this->inputArguments, $this->invocationChainMock);
        $expectedResult = array(
            array(
                'label' => 'associatedProductName',
                'value' => 1
            ),
            array(
                'label' => 'productName',
                'value' => 2
            )
        );
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @covers Magento\GroupedProduct\Helper\Product\Configuration\Plugin\Grouped::aroundGetOptions
     */
    public function testAroundGetOptionsGroupedProductWithoutAssociated()
    {
        $this->typeInstanceMock->expects($this->once())
            ->method('getAssociatedProducts')
            ->with($this->productMock)
            ->will($this->returnValue(false));

        $this->productMock->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue(\Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE));

        $chainCallResult = array(array('label' => 'label', 'value' => 'value'));

        $this->invocationChainMock->expects($this->once())
            ->method('proceed')
            ->with($this->inputArguments)
            ->will($this->returnValue($chainCallResult));

        $result = $this->groupedConfigPlugin->aroundGetOptions($this->inputArguments, $this->invocationChainMock);
        $this->assertEquals($chainCallResult, $result);
    }

    /**
     * @covers Magento\GroupedProduct\Helper\Product\Configuration\Plugin\Grouped::aroundGetOptions
     */
    public function testAroundGetOptionsAnotherProductType()
    {
        $chainCallResult = array('result');

        $this->productMock->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue('other_product_type'));

        $this->invocationChainMock->expects($this->once())
            ->method('proceed')
            ->with($this->inputArguments)
            ->will($this->returnValue($chainCallResult));

        $this->productMock->expects($this->never())
            ->method('getTypeInstance');

        $result = $this->groupedConfigPlugin->aroundGetOptions($this->inputArguments, $this->invocationChainMock);
        $this->assertEquals($chainCallResult, $result);
    }
}

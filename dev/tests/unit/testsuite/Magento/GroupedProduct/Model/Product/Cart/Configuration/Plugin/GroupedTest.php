<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Model\Product\Cart\Configuration\Plugin;

class GroupedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GroupedProduct\Model\Product\Cart\Configuration\Plugin\Grouped
     */
    protected $groupedPlugin;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $invocationChainMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    protected function setUp()
    {
        $this->invocationChainMock = $this->getMock('Magento\Code\Plugin\InvocationChain', array(), array(), '', false);
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $this->groupedPlugin = new \Magento\GroupedProduct\Model\Product\Cart\Configuration\Plugin\Grouped();
    }

    public function testAroundIsProductConfiguredWhenProductGrouped()
    {
        $config = array('super_group' => 'product');
        $this->productMock
            ->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue(\Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE));
        $this->invocationChainMock->expects($this->never())->method('proceed');
        $this->assertEquals(true,
            $this->groupedPlugin->aroundIsProductConfigured(
                array($this->productMock, $config), $this->invocationChainMock));
    }

    public function testAroundIsProductConfiguredWhenProductIsNotGrouped()
    {
        $config = array('super_group' => 'product');
        $this->productMock
            ->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue('product'));
        $this->invocationChainMock->expects($this->once())->method('proceed')->with(array($this->productMock, $config));
        $this->groupedPlugin->aroundIsProductConfigured(array($this->productMock, $config), $this->invocationChainMock);
    }
}


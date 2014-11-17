<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Model\Plugin;

class BundleOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BundleOptions
     */
    protected $plugin;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $writeServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $readServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $optionBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $linkBuilderMock;

    protected function setUp()
    {
        $this->writeServiceMock =
            $this->getMock('Magento\Bundle\Service\V1\Product\Option\WriteService', [], [], '', false);
        $this->readServiceMock =
            $this->getMock('Magento\Bundle\Service\V1\Product\Option\ReadService', [], [], '', false);
        $this->productBuilderMock = $this->getMock('Magento\Catalog\Api\Data\ProductDataBuilder', [], [], '', false);
        $this->optionBuilderMock =
            $this->getMock('Magento\Bundle\Service\V1\Data\Product\OptionBuilder', [], [], '', false);
        $this->linkBuilderMock =
            $this->getMock('Magento\Bundle\Service\V1\Data\Product\LinkBuilder', [], [], '', false);
        $this->plugin = new BundleOptions(
            $this->writeServiceMock,
            $this->readServiceMock,
            $this->productBuilderMock,
            $this->optionBuilderMock,
            $this->linkBuilderMock
        );
    }

    public function testAroundGet()
    {
        $sku = 'productSku';
        $editMode = false;
        $productRepositoryMock = $this->getMock('\Magento\Catalog\Api\ProductRepositoryInterface');
        $productMock = $this->getMock('\Magento\Catalog\Api\Data\ProductInterface');
        $closure = function () use ($productMock) {
            return $productMock;
        };

        $productMock->expects($this->once())
            ->method('getTypeId')
            ->willReturn(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE);

        $optionMock = $this->getMock('\Magento\Bundle\Service\V1\Data\Product\Option', [], [], '', false);
        $this->readServiceMock->expects($this->once())
            ->method('getListForProduct')
            ->with($productMock)
            ->willReturn([$optionMock]);

        $this->productBuilderMock->expects($this->once())->method('populate')->with($productMock)->willReturnSelf();
        $this->productBuilderMock->expects($this->once())
            ->method('setCustomAttribute')
            ->with('bundle_product_options', [$optionMock])
            ->willReturnSelf();

        $newProductMock = $this->getMock('\Magento\Catalog\Api\Data\ProductInterface');
        $this->productBuilderMock->expects($this->once())->method('create')->willReturn($newProductMock);

        $this->assertEquals(
            $newProductMock,
            $this->plugin->aroundGet($productRepositoryMock, $closure, $sku, $editMode)
        );
    }

    public function testAroundGetIfProductTypeNotBundle()
    {
        $sku = 'productSku';
        $editMode = false;
        $productRepositoryMock = $this->getMock('\Magento\Catalog\Api\ProductRepositoryInterface');
        $productMock = $this->getMock('\Magento\Catalog\Api\Data\ProductInterface');
        $closure = function () use ($productMock) {
            return $productMock;
        };

        $productMock->expects($this->once())
            ->method('getTypeId')
            ->willReturn(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);

        $this->assertEquals(
            $productMock,
            $this->plugin->aroundGet($productRepositoryMock, $closure, $sku, $editMode)
        );
    }
}
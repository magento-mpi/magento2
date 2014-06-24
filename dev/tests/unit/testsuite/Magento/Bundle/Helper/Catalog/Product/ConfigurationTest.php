<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Helper\Catalog\Product;

use Magento\TestFramework\Helper\ObjectManager;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreData;

    /**
     * @var \Magento\Catalog\Helper\Product\Configuration|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productConfiguration;

    /**
     * @var \Magento\Framework\Escaper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $escaper;

    /**
     * @var \Magento\Bundle\Helper\Catalog\Product\Configuration
     */
    protected $helper;

    protected function setUp()
    {
        $this->coreData = $this->getMock('Magento\Core\Helper\Data', [], [], '', false);
        $this->productConfiguration = $this->getMock('Magento\Catalog\Helper\Product\Configuration', [], [], '', false);
        $this->escaper = $this->getMock( 'Magento\Framework\Escaper', [], [], '', false);

        $this->helper = (new ObjectManager($this))->getObject('Magento\Bundle\Helper\Catalog\Product\Configuration', [
            'coreData' => $this->coreData,
            'productConfiguration' => $this->productConfiguration,
            'escaper' => $this->escaper,
        ]);
    }

    public function testGetSelectionQty()
    {
        $selectionId = 15;
        $selectionQty = 35;
        $product = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $option = $this->getMock('Magento\Catalog\Model\Product\Option', ['__wakeup', 'getValue'], [], '', false);

        $product->expects($this->once())->method('getCustomOption')->with('selection_qty_' . $selectionId)
            ->will($this->returnValue($option));
        $option->expects($this->once())->method('getValue')->will($this->returnValue($selectionQty));

        $this->assertEquals($selectionQty, $this->helper->getSelectionQty($product, $selectionId));
    }

    public function testGetSelectionQtyIfCustomOptionIsNotSet()
    {
        $selectionId = 15;
        $product = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);

        $product->expects($this->once())->method('getCustomOption')->with('selection_qty_' . $selectionId)
            ->will($this->returnValue(null));

        $this->assertEquals(0, $this->helper->getSelectionQty($product, $selectionId));
    }

    /**
     * @covers \Magento\Bundle\Helper\Catalog\Product\Configuration::getSelectionFinalPrice
     */
    public function testGetSelectionFinalPrice()
    {
        $itemQty = 2;
        $item = $this->getMock(
            'Magento\Catalog\Model\Product\Configuration\Item\ItemInterface',
            ['getQty', 'getProduct', 'getOptionByCode', 'getFileDownloadParams']
        );
        $product = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $price = $this->getMock('Magento\Bundle\Model\Product\Price', [], [], '', false);
        $selectionProduct = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);

        $selectionProduct->expects($this->once())->method('unsetData')->with('final_price');
        $item->expects($this->once())->method('getProduct')->will($this->returnValue($product));
        $item->expects($this->once())->method('getQty')->will($this->returnValue($itemQty));
        $product->expects($this->once())->method('getPriceModel')->will($this->returnValue($price));
        $price->expects($this->once())->method('getSelectionFinalTotalPrice')
            ->with($product, $selectionProduct, $itemQty, 0, false, true);

        $this->helper->getSelectionFinalPrice($item, $selectionProduct);
    }
}

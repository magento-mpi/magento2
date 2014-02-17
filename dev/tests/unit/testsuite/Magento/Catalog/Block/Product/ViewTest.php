<?php
/**
 * Test class for \Magento\Catalog\Block\Product\View
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Block\Product;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Block\Product\View
     */
    protected $view;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productTypeConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->productTypeConfig = $this->getMock('Magento\Catalog\Model\ProductTypes\ConfigInterface');
        $this->registryMock = $this->getMock('Magento\Core\Model\Registry', array(), array(), '', false);
        $this->view = $helper->getObject('Magento\Catalog\Block\Product\View', array(
                'productTypeConfig' => $this->productTypeConfig,
                'registry' => $this->registryMock
            )
        );
    }

    public function testShouldRenderQuantity()
    {
        $productMock = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $this->registryMock
            ->expects($this->any())
            ->method('registry')
            ->with('product')
            ->will($this->returnValue($productMock));
        $productMock->expects($this->once())->method('getTypeId')->will($this->returnValue('id'));
        $this->productTypeConfig
            ->expects($this->once())
            ->method('isProductSet')
            ->with('id')
            ->will($this->returnValue(true));
        $this->assertEquals(false, $this->view->shouldRenderQuantity());
    }

    public function testGetIdentities()
    {
        $productTags = array('catalog_product_1');
        $product = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $product->expects($this->once())
            ->method('getIdentities')
            ->will($this->returnValue($productTags));
        $this->registryMock->expects($this->any())
            ->method('registry')
            ->with('product')
            ->will($this->returnValue($product));
        $this->assertEquals(
            $productTags,
            $this->view->getIdentities()
        );
    }
}

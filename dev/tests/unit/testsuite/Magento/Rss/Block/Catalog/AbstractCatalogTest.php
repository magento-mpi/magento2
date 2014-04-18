<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rss\Block\Catalog;

/**
 * Test for rendering price html in rss templates
 *
 * @package Magento\Rss\Block\Catalog
 */
class AbstractCatalogTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test renderPriceHtml function
     */
    public function testRenderPriceHtml()
    {
        $priceHtmlForTest = '<html>Price is 10 for example</html>';
        $templateContextMock = $this->getMock('Magento\Framework\View\Element\Template\Context', [], [], '', false);
        $httpContextMock = $this->getMock('Magento\Framework\App\Http\Context', [], [], '', false);
        $helperMock = $this->getMock('Magento\Catalog\Helper\Data', [], [], '', false);
        $productMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $layoutMock = $this->getMockForAbstractClass(
            'Magento\Framework\View\LayoutInterface',
            [],
            '',
            true,
            true,
            true,
            ['getBlock']
        );
        $priceRendererMock = $this->getMock('Magento\Pricing\Render', ['render'], [], '', false);

        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->will($this->returnValue($priceRendererMock));
        $priceRendererMock->expects($this->once())
            ->method('render')
            ->will($this->returnValue($priceHtmlForTest));

        $block = new \Magento\Rss\Block\Catalog\AbstractCatalog(
            $templateContextMock,
            $httpContextMock,
            $helperMock
        );
        $block->setLayout($layoutMock);

        $this->assertEquals($priceHtmlForTest, $block->renderPriceHtml($productMock, true));
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rss\Block\Catalog;

/**
 * Test for rendering price html in rss templates
 *
 * @package Magento\Rss\Block\Catalog
 */
class NewCatalogTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rss\Block\Catalog\NewCatalog
     */
    protected $block;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelperMock;

    /**
     * Set up mock objects for tested class
     */
    public function setUp()
    {
        $templateContextMock = $this->getMock('Magento\View\Element\Template\Context', [], [], '', false);
        $this->imageHelperMock = $this->getMock('Magento\Catalog\Helper\Image', [], [], '', false);

        $eventManagerMock = $this->getMock('Magento\Event\ManagerInterface', [], [], '', false);
        $requestMock = $this->getMock('Magento\Framework\App\RequestInterface', [], [], '', false);

        $templateContextMock->expects($this->once())
            ->method('getEventManager')
            ->will($this->returnValue($eventManagerMock));
        $templateContextMock->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($requestMock));

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->block = $objectManager->getObject(
            'Magento\Rss\Block\Catalog\NewCatalog',
            [
                'context' => $templateContextMock,
                'imageHelper' => $this->imageHelperMock,
            ]
        );
    }

    /**
     * Test for method addNewItemXmlCallback
     */
    public function testAddNewItemXmlCallback()
    {
        $priceHtmlForTest = '<div class="price">Price is 10 for example</div>';
        $productMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['getProductUrl', 'getDescription', 'getAllowedInRss', 'getName', '__wakeup'],
            [],
            '',
            false
        );
        $rssObjMock = $this->getMock('Magento\Rss\Model\Rss', [], [], '', false);
        $layoutMock = $this->getMockForAbstractClass(
            'Magento\View\LayoutInterface',
            [],
            '',
            true,
            true,
            true,
            ['getBlock']
        );
        $priceRendererMock = $this->getMock('Magento\Pricing\Render', ['render'], [], '', false);
        $productTitle = 'Product title';
        $productUrl = '<a href="http://product.url">Product Url</a>';
        $imgThumbSrc = 'http://source-for-thumbnail';
        $productDescription = 'Product description';
        $description = '<table><tr><td><a href="' . $productUrl . '"><img src="' . $imgThumbSrc .
            '" border="0" align="left" height="75" width="75"></a></td><td  style="text-decoration:none;">' .
            $productDescription . $priceHtmlForTest . '</td></tr></table>';

        $productMock->expects($this->exactly(2))
            ->method('getProductUrl')
            ->will($this->returnValue($productUrl));
        $productMock->expects($this->once())
            ->method('getDescription')
            ->will($this->returnValue($productDescription));
        $productMock->expects($this->any())
            ->method('getAllowedInRss')
            ->will($this->returnValue(true));
        $productMock->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($productTitle));
        $this->imageHelperMock->expects($this->once())
            ->method('init')
            ->will($this->returnSelf());
        $this->imageHelperMock->expects($this->once())
            ->method('resize')
            ->will($this->returnValue($imgThumbSrc));
        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->will($this->returnValue($priceRendererMock));
        $priceRendererMock->expects($this->once())
            ->method('render')
            ->will($this->returnValue($priceHtmlForTest));

        $expectedData = [
            'title' => $productTitle,
            'link' => $productUrl,
            'description' => $description
        ];
        $rssObjMock->expects($this->once())
            ->method('_addEntry')
            ->with($expectedData)
            ->will($this->returnSelf());


        $args = [
            'product' => $productMock,
            'rssObj' => $rssObjMock,
            'row' => ''
        ];

        $this->block->setLayout($layoutMock);
        $this->assertNull($this->block->addNewItemXmlCallback($args));
    }
}

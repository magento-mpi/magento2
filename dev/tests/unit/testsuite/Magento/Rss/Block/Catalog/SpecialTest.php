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
class SpecialTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rss\Block\Catalog\Special
     */
    protected $block;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelperMock;

    /**
     * @var \Magento\Core\Model\Store
     */
    protected $storeMock;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactoryMock;

    /**
     * @var \Magento\Rss\Model\RssFactory
     */
    protected $rssFactoryMock;

    /**
     * @var \Magento\Model\Resource\Iterator
     */
    protected $resourceIteratorMock;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogHelperMock;

    /**
     * @var \Magento\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrencyMock;

    /**
     * Set up mock objects for tested class
     *
     * @return void
     */
    public function setUp()
    {
        $templateContextMock = $this->getMock('Magento\View\Element\Template\Context', [], [], '', false);
        $httpContextMock = $this->getMock('Magento\App\Http\Context', [], [], '', false);
        $this->catalogHelperMock = $this->getMock('Magento\Catalog\Helper\Data', [], [], '', false);
        $catalogOutputMock = $this->getMock('Magento\Catalog\Helper\Output', [], [], '', false);
        $this->priceCurrencyMock = $this->getMockForAbstractClass(
            'Magento\Pricing\PriceCurrencyInterface',
            [],
            '',
            true,
            true,
            true,
            ['convertAndFormat']
        );
        $this->rssFactoryMock = $this->getMock('Magento\Rss\Model\RssFactory', ['create'], [], '', false);
        $this->productFactoryMock = $this->getMock('Magento\Catalog\Model\ProductFactory', ['create'], [], '', false);
        $this->resourceIteratorMock = $this->getMock('Magento\Model\Resource\Iterator', [], [], '', false);
        $this->imageHelperMock = $this->getMock('Magento\Catalog\Helper\Image', [], [], '', false);

        $eventManagerMock = $this->getMock('Magento\Event\ManagerInterface', [], [], '', false);
        $requestMock = $this->getMock('Magento\App\RequestInterface', [], [], '', false);
        $storeManagerMock = $this->getMock('Magento\Core\Model\StoreManagerInterface', [], [], '', false);
        $urlBuilderMock = $this->getMock('Magento\UrlInterface', [], [], '', false);
        $this->storeMock = $this->getMock('Magento\Core\Model\Store', [], [], '', false);
        $storeConfigMock = $this->getMock('Magento\Core\Model\Store\Config', [], [], '', false);
        $cacheStateMock = $this->getMock('Magento\App\Cache\StateInterface', [], [], '', false);

        $templateContextMock->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($requestMock));
        $templateContextMock->expects($this->any())
            ->method('getStoreConfig')
            ->will($this->returnValue($storeConfigMock));
        $templateContextMock->expects($this->any())
            ->method('getCacheState')
            ->will($this->returnValue($cacheStateMock));
        $templateContextMock->expects($this->any())
            ->method('getEventManager')
            ->will($this->returnValue($eventManagerMock));
        $templateContextMock->expects($this->any())
            ->method('getUrlBuilder')
            ->will($this->returnValue($urlBuilderMock));
        $templateContextMock->expects($this->any())
            ->method('getStoreManager')
            ->will($this->returnValue($storeManagerMock));
        $storeManagerMock->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($this->storeMock));
        $this->storeMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(0));


        $this->block = new \Magento\Rss\Block\Catalog\Special(
            $templateContextMock,
            $httpContextMock,
            $this->catalogHelperMock,
            $this->priceCurrencyMock,
            $this->productFactoryMock,
            $this->rssFactoryMock,
            $this->resourceIteratorMock,
            $this->imageHelperMock,
            $catalogOutputMock
        );
    }

    /**
     * Test for method _toHtml
     *
     * @return void
     */
    public function testToHtml()
    {
        $productMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['getProductUrl', 'getDescription', 'getAllowedPriceInRss', 'getName', '__wakeup', 'getResourceCollection'],
            [],
            '',
            false
        );
        $productCollectionMock = $this->getMock(
            'Magento\Catalog\Model\Resource\Product\CollectionFactory',
            ['addPriceDataFieldFilter', 'addPriceData', 'addAttributeToSelect', 'addAttributeToSort', 'getSelect'],
            [],
            '',
            false
        );
        $rssObjMock = $this->getMock('Magento\Rss\Model\Rss', [], [], '', false);
        $productUrl = '<a href="http://product.url">Product Url</a>';
        $imgThumbSrc = 'http://source-for-thumbnail';
        $productTitle = 'Product title';
        $basePriceFormatted = '<span class="price">$10.00</span>';
        $finalPriceFormatted = '<span class="price">$20.00</span>';
        $productDescription = '<table><tr>' .
            '<td><a href="' . $productUrl . '"><img src="' . $imgThumbSrc .
            '" alt="" border="0" align="left" height="75" width="75" /></a></td>' .
            '<td style="text-decoration:none;"><p>Price: ' . $basePriceFormatted . ' Special Price: ' .
            $finalPriceFormatted . '</p></td></tr></table>';
        $expectedData = [
            'title' => $productTitle,
            'link' => $productUrl,
            'description' => $productDescription
        ];
        $expectedResult = new \Magento\Object([
            'rss_feed' => '<xml>Feed of the rss</xml>'
        ]);

        $this->addMocks();
        $this->productFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($productMock));
        $productMock->expects($this->once())
            ->method('getResourceCollection')
            ->will($this->returnValue($productCollectionMock));
        $productCollectionMock->expects($this->once())
            ->method('addPriceDataFieldFilter')
            ->will($this->returnSelf());
        $productCollectionMock->expects($this->once())
            ->method('addPriceData')
            ->will($this->returnSelf());
        $productCollectionMock->expects($this->once())
            ->method('addAttributeToSelect')
            ->will($this->returnSelf());
        $productCollectionMock->expects($this->once())
            ->method('addAttributeToSort')
            ->will($this->returnSelf());
        $this->rssFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($rssObjMock));
        $productMock->expects($this->exactly(2))
            ->method('getProductUrl')
            ->will($this->returnValue($productUrl));
        $this->imageHelperMock->expects($this->once())
            ->method('resize')
            ->will($this->returnValue($imgThumbSrc));
        $productMock->expects($this->any())
            ->method('getAllowedPriceInRss')
            ->will($this->returnValue(true));
        $productMock->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($productTitle));
        $this->priceCurrencyMock->expects($this->exactly(2))
            ->method('convertAndFormat')
            ->will($this->returnValueMap(
                [
                    [
                        10,
                        true,
                        \Magento\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION,
                        null,
                        null,
                        $basePriceFormatted
                    ],
                    [
                        20,
                        true,
                        \Magento\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION,
                        null,
                        null,
                        $finalPriceFormatted
                    ],
                ]
            )
        );
        $rssObjMock->expects($this->once())
            ->method('_addEntry')
            ->with($expectedData)
            ->will($this->returnSelf());
        $rssObjMock->expects($this->once())
            ->method('createRssXml')
            ->will($this->returnValue($expectedResult));

        $this->assertEquals($expectedResult, $this->block->toHtml());
    }

    /**
     * Additional function to break up mocks initialization
     *
     * @return void
     */
    protected function addMocks()
    {
        $resIteratorcallback = function($select, $callbacks, $args) {
            $args['results'] = [
                ['use_special' => false, 'price' => 10, 'final_price' => 20]
            ];
        };

        $this->storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->will($this->returnValue(0));
        $this->storeMock->expects($this->once())
            ->method('getFrontendName')
            ->will($this->returnValue('store name'));

        $this->catalogHelperMock->expects($this->once())
            ->method('canApplyMsrp')
            ->will($this->returnValue(false));
        $this->resourceIteratorMock->expects($this->once())
            ->method('walk')
            ->will($this->returnCallback($resIteratorcallback));
        $this->imageHelperMock->expects($this->once())
            ->method('init')
            ->will($this->returnSelf());
    }
}

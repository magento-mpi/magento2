<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rss\Block;

/**
 * Test for rendering price html in rss templates
 *
 * @package Magento\Rss\Block
 */
class WishlistTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rss\Block\Catalog\Category
     */
    protected $block;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactoryMock;

    /**
     * @var \Magento\Rss\Model\RssFactory
     */
    protected $rssFactoryMock;

    /**
     * @var \Magento\UrlInterface
     */
    protected $urlBuilderMock;

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $coreHelperMock;

    /**
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $wishlistHelperMock;

    /**
     * @var \Magento\Core\Model\Store\Config
     */
    protected $storeConfigMock;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelperMock;

    /**
     * @var \Magento\Catalog\Helper\Output
     */
    protected $catalogOutputMock;

    /**
     * Set up mock objects for tested class
     *
     * @return void
     */
    public function setUp()
    {
        $templateContextMock = $this->getMock('Magento\Catalog\Block\Product\Context', [], [], '', false);
        $httpContextMock = $this->getMock('Magento\App\Http\Context', [], [], '', false);
        $this->coreHelperMock = $this->getMock('Magento\Core\Helper\Data', [], [], '', false);
        $this->catalogOutputMock = $this->getMock('Magento\Catalog\Helper\Output', [], [], '', false);
        $wishlistFactoryMock = $this->getMock('Magento\Wishlist\Model\WishlistFactory', [], [], '', false);
        $this->rssFactoryMock = $this->getMock('Magento\Rss\Model\RssFactory', ['create'], [], '', false);
        $eventManagerMock = $this->getMock('Magento\Event\ManagerInterface', [], [], '', false);
        $cacheStateMock = $this->getMock('Magento\App\Cache\StateInterface', [], [], '', false);
        $this->productFactoryMock = $this->getMock(
            'Magento\Catalog\Model\ProductFactory',
            ['create', '__wakeup'],
            [],
            '',
            false
        );
        $this->wishlistHelperMock = $this->getMock(
            'Magento\Wishlist\Helper\Data',
            ['getWishlist', 'getCustomer', 'getCustomerName'],
            [],
            '',
            false
        );
        $this->urlBuilderMock = $this->getMockForAbstractClass(
            'Magento\UrlInterface',
            [],
            '',
            true,
            true,
            true,
            ['getUrl']
        );
        $this->storeConfigMock = $this->getMock(
            'Magento\Core\Model\Store\Config',
            ['getConfig'],
            [],
            '',
            false
        );
        $this->imageHelperMock = $this->getMock('Magento\Catalog\Helper\Image', [], [], '', false);

        $templateContextMock->expects($this->once())
            ->method('getEventManager')
            ->will($this->returnValue($eventManagerMock));
        $templateContextMock->expects($this->once())
            ->method('getCacheState')
            ->will($this->returnValue($cacheStateMock));
        $templateContextMock->expects($this->once())
            ->method('getImageHelper')
            ->will($this->returnValue($this->imageHelperMock));
        $templateContextMock->expects($this->once())
            ->method('getWishlistHelper')
            ->will($this->returnValue($this->wishlistHelperMock));
        $templateContextMock->expects($this->once())
            ->method('getStoreConfig')
            ->will($this->returnValue($this->storeConfigMock));
        $templateContextMock->expects($this->once())
            ->method('getUrlBuilder')
            ->will($this->returnValue($this->urlBuilderMock));

        $this->block = new \Magento\Rss\Block\Wishlist(
            $templateContextMock,
            $httpContextMock,
            $this->productFactoryMock,
            $this->coreHelperMock,
            $wishlistFactoryMock,
            $this->rssFactoryMock,
            $this->catalogOutputMock
        );
    }

    /**
     * Test for method _toHtml
     */
    public function testToHtml()
    {
        $wishlistId = 1;
        $customerName = 'Customer Name';
        $title = "$customerName's Wishlist";
        $rssObjMock = $this->getMock('Magento\Rss\Model\Rss', [], [], '', false);
        $wishlistModelMock = $this->getMock(
            'Magento\Wishlist\Model\Wishlist',
            ['getId', '__wakeup', 'getCustomerId', 'getItemCollection'],
            [],
            '',
            false
        );
        $customerServiceMock = $this->getMock('Magento\Customer\Service\V1\Data\Customer', [], [], '', false);
        $wishlistSharingUrl = 'wishlist/shared/index/1';
        $locale = 'en_US';
        $productUrl = 'http://product.url/';
        $productName = 'Product name';
        $expectedHeaders = [
            'title' => $title,
            'description' => $title,
            'link' => $wishlistSharingUrl,
            'charset' => 'UTF-8',
            'language' => $locale
        ];


        $this->rssFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($rssObjMock));
        $this->wishlistHelperMock->expects($this->once())
            ->method('getWishlist')
            ->will($this->returnValue($wishlistModelMock));
        $this->wishlistHelperMock->expects($this->once())
            ->method('getCustomer')
            ->will($this->returnValue($customerServiceMock));
        $this->wishlistHelperMock->expects($this->once())
            ->method('getCustomerName')
            ->will($this->returnValue($customerName));
        $wishlistModelMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($wishlistId));
        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue($wishlistSharingUrl));
        $this->storeConfigMock->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValueMap(
                    [
                        ['advanced/modules_disable_output/Magento_Rss', null, null],
                        ['general/locale/code', null, $locale]
                    ]
                )
            );

        $staticArgs = [
            'productName' => $productName,
            'productUrl' => $productUrl
        ];
        $description = $this->processWishlistItemDescription($wishlistModelMock, $staticArgs);
        $expectedEntry = [
            'title' => $productName,
            'link' => $productUrl,
            'description' => $description
        ];
        $rssString = '';

        $rssObjMock->expects($this->once())
            ->method('_addHeader')
            ->with($expectedHeaders)
            ->will($this->returnSelf());
        $rssObjMock->expects($this->once())
            ->method('_addEntry')
            ->with($expectedEntry);
        $rssObjMock->expects($this->once())
            ->method('createRssXml')
            ->will($this->returnValue($rssString));

        $this->assertEquals($rssString, $this->block->toHtml());
    }

    /**
     * Additional function to process forming description for wishlist item
     *
     * @param \Magento\Wishlist\Model\Wishlist $wishlistModelMock
     * @param array $staticArgs
     * @return string
     */
    protected function processWishlistItemDescription($wishlistModelMock, $staticArgs)
    {
        $imgThumbSrc = 'http://source-for-thumbnail';
        $priceHtmlForTest = '<div class="price">Price is 10 for example</div>';
        $productDescription = 'Product description';
        $productShortDescription = 'Product short description';

        $wishlistItem = $this->getMock('Magento\Wishlist\Model\Item', [], [], '', false);
        $wishlistItemsCollection = [
            $wishlistItem
        ];
        $productMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            [
                'getAllowedInRss',
                'getAllowedPriceInRss',
                'getDescription',
                'getShortDescription',
                'getName',
                'getVisibleInSiteVisibilities',
                'getUrlModel',
                '__wakeup'
            ],
            [],
            '',
            false
        );
        $urlModelMock = $this->getMock('Magento\Catalog\Model\Product\Url', [], [], '', false);
        $layoutMock = $this->getMockForAbstractClass(
            'Magento\View\LayoutInterface',
            [],
            '',
            true,
            true,
            true,
            ['getBlock']
        );

        $wishlistModelMock->expects($this->once())
            ->method('getItemCollection')
            ->will($this->returnValue($wishlistItemsCollection));
        $wishlistItem->expects($this->once())
            ->method('getProduct')
            ->will($this->returnValue($productMock));
        $productMock->expects($this->once())
            ->method('getUrlModel')
            ->will($this->returnValue($urlModelMock));
        $productMock->expects($this->once())
            ->method('getAllowedPriceInRss')
            ->will($this->returnValue($urlModelMock));
        $urlModelMock->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue($staticArgs['productUrl']));
        $productMock->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($staticArgs['productName']));
        $productMock->expects($this->once())
            ->method('getAllowedInRss')
            ->will($this->returnValue(true));
        $productMock->expects($this->once())
            ->method('getVisibleInSiteVisibilities')
            ->will($this->returnValue(true));
        $this->imageHelperMock->expects($this->once())
            ->method('init')
            ->will($this->returnSelf());
        $this->imageHelperMock->expects($this->once())
            ->method('resize')
            ->will($this->returnValue($imgThumbSrc));
        $priceRendererMock = $this->getMock('Magento\Pricing\Render', ['render'], [], '', false);

        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->will($this->returnValue($priceRendererMock));
        $priceRendererMock->expects($this->once())
            ->method('render')
            ->will($this->returnValue($priceHtmlForTest));
        $productMock->expects($this->any())
            ->method('getDescription')
            ->will($this->returnValue($productDescription));
        $productMock->expects($this->any())
            ->method('getShortDescription')
            ->will($this->returnValue($productShortDescription));
        $this->catalogOutputMock->expects($this->any())
            ->method('productAttribute')
            ->will($this->returnArgument(1));

        $this->block->setLayout($layoutMock);
        $description = '<table><tr><td><a href="' . $staticArgs['productUrl'] . '"><img src="' . $imgThumbSrc .
            '" border="0" align="left" height="75" width="75"></a></td><td style="text-decoration:none;">' .
            $productShortDescription . '<p>' . $priceHtmlForTest . '</p><p>Comment: ' . $productDescription . '<p>' .
            '</td></tr></table>';

        return $description;
    }

    /**
     * Test for method _toHtml for the case, when wishlist is absent
     */
    public function testToHtmlWithoutWishlist()
    {
        $url = 'http://base.url/index';
        $rssString = '<xml>Some empty xml</xml>';
        $rssObjMock = $this->getMock('Magento\Rss\Model\Rss', [], [], '', false);
        $customerServiceMock = $this->getMock('Magento\Customer\Service\V1\Data\Customer', [], [], '', false);
        $wishlistModelMock = $this->getMock(
            'Magento\Wishlist\Model\Wishlist',
            ['getId', '__wakeup', 'getCustomerId'],
            [],
            '',
            false
        );
        $expectedHeaders = [
            'title' => __('We cannot retrieve the wish list.'),
            'description' => __('We cannot retrieve the wish list.'),
            'link' => $url,
            'charset' => 'UTF-8'
        ];

        $this->rssFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($rssObjMock));
        $this->wishlistHelperMock->expects($this->once())
            ->method('getWishlist')
            ->will($this->returnValue($wishlistModelMock));
        $wishlistModelMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(false));
        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue($url));
        $this->wishlistHelperMock->expects($this->once())
            ->method('getCustomer')
            ->will($this->returnValue($customerServiceMock));
        $rssObjMock->expects($this->once())
            ->method('_addHeader')
            ->with($expectedHeaders)
            ->will($this->returnSelf());
        $rssObjMock->expects($this->once())
            ->method('createRssXml')
            ->will($this->returnValue($rssString));

        $this->assertEquals($rssString, $this->block->toHtml());
    }
}

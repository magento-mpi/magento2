<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Banner\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Banner\Model\Observer
     */
    protected $observer;

    /**
     * @var \Magento\Framework\Event\Observer
     */
    protected $eventObserver;

    /**
     * @var \Magento\Backend\Helper\Js|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $adminhtmlJs;

    /**
     * @var \Magento\Banner\Model\Resource\BannerFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $bannerFactory;

    /**
     * @var \Magento\Framework\Event|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $event;

    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $http;

    protected function setUp()
    {
        $this->adminhtmlJs = $this->getMockBuilder('Magento\Backend\Helper\Js')
            ->disableOriginalConstructor()
            ->getMock();
        $this->bannerFactory = $this->getMockBuilder('Magento\Banner\Model\Resource\BannerFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->observer = new Observer(
            $this->adminhtmlJs,
            $this->bannerFactory
        );
    }

    public function testPrepareCatalogRuleSave()
    {
        $this->http = $this->getMockBuilder('Magento\Framework\App\Request\Http')
            ->disableOriginalConstructor()
            ->getMock();
        $this->http->expects($this->any())->method('getPost')->with('related_banners')->will(
            $this->returnValue('test')
        );
        $this->adminhtmlJs->expects($this->once())->method('decodeGridSerializedInput')->with('test')->will(
            $this->returnValue('test')
        );
        $this->http->expects($this->any())->method('setPost')->with('related_banners', 'test');
        $this->event = $this->getMockBuilder('Magento\Framework\Event')
            ->disableOriginalConstructor()
            ->setMethods(['getRequest', 'setPost', 'getPost'])
            ->getMock();
        $this->event->expects($this->any())->method('getRequest')->will($this->returnValue($this->http));
        $this->eventObserver = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();
        $this->eventObserver->expects($this->any())->method('getEvent')->will($this->returnValue($this->event));
        $this->assertInstanceOf(
            '\Magento\Banner\Model\Observer',
            $this->observer->prepareCatalogRuleSave($this->eventObserver)
        );
    }

    /**
     * @param [] $banners
     *
     * @dataProvider testBindRelatedBannersDataProvider
     */
    public function testBindRelatedBannersToCatalogRule($banners)
    {
        $this->event = $this->getMockBuilder('Magento\Framework\Event')
            ->disableOriginalConstructor()
            ->setMethods(['getRule', 'getId'])
            ->getMock();
        $this->http = $this->getMockBuilder('Magento\Framework\App\Request\Http')
            ->disableOriginalConstructor()
            ->setMethods(['getRelatedBanners', 'getId'])
            ->getMock();
        $banner = $this->getMockBuilder('Magento\Banner\Model\Resource\Banner')
            ->disableOriginalConstructor()
            ->setMethods(['bindBannersToCatalogRule'])
            ->getMock();
        $banner->expects($this->once())->method('bindBannersToCatalogRule')->with(1, $banners)->will(
            $this->returnSelf()
        );
        $this->event->expects($this->any())->method('getRule')->will($this->returnValue($this->http));
        $this->http->expects($this->once())->method('getId')->will($this->returnValue(1));
        $this->http->expects($this->any())->method('getRelatedBanners')->will(
            $this->returnValue($banners)
        );
        $this->eventObserver = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();
        $this->eventObserver->expects($this->any())->method('getEvent')->will($this->returnValue($this->event));
        $this->bannerFactory->expects($this->once())->method('create')->will($this->returnValue($banner));
        $this->assertInstanceOf(
            '\Magento\Banner\Model\Observer',
            $this->observer->bindRelatedBannersToCatalogRule($this->eventObserver)
        );
    }

    /**
     * @param [] $banners
     *
     * @dataProvider testBindRelatedBannersDataProvider
     */
    public function testBindRelatedBannersToSalesRule($banners)
    {
        $this->event = $this->getMockBuilder('Magento\Framework\Event')
            ->disableOriginalConstructor()
            ->setMethods(['getRule', 'getId'])
            ->getMock();
        $this->http = $this->getMockBuilder('Magento\Framework\App\Request\Http')
            ->disableOriginalConstructor()
            ->setMethods(['getRelatedBanners', 'getId'])
            ->getMock();
        $banner = $this->getMockBuilder('Magento\Banner\Model\Resource\Banner')
            ->disableOriginalConstructor()
            ->setMethods(['bindBannersToSalesRule'])
            ->getMock();
        $banner->expects($this->once())->method('bindBannersToSalesRule')->with(1, $banners)->will(
            $this->returnSelf()
        );
        $this->event->expects($this->any())->method('getRule')->will($this->returnValue($this->http));
        $this->http->expects($this->once())->method('getId')->will($this->returnValue(1));
        $this->http->expects($this->any())->method('getRelatedBanners')->will(
            $this->returnValue($banners)
        );
        $this->eventObserver = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();
        $this->eventObserver->expects($this->any())->method('getEvent')->will($this->returnValue($this->event));
        $this->bannerFactory->expects($this->once())->method('create')->will($this->returnValue($banner));
        $this->assertInstanceOf(
            '\Magento\Banner\Model\Observer',
            $this->observer->bindRelatedBannersToSalesRule($this->eventObserver)
        );
    }

    public function testPrepareSalesRuleSave()
    {
        $this->http = $this->getMockBuilder('Magento\Framework\App\Request\Http')
            ->disableOriginalConstructor()
            ->getMock();
        $this->http->expects($this->any())->method('getPost')->with('related_banners')->will(
            $this->returnValue('test')
        );
        $this->adminhtmlJs->expects($this->once())->method('decodeGridSerializedInput')->with('test')->will(
            $this->returnValue('test')
        );
        $this->http->expects($this->any())->method('setPost')->with('related_banners', 'test');
        $this->event = $this->getMockBuilder('Magento\Framework\Event')
            ->disableOriginalConstructor()
            ->setMethods(['getRequest', 'setPost', 'getPost'])
            ->getMock();
        $this->event->expects($this->any())->method('getRequest')->will($this->returnValue($this->http));
        $this->eventObserver = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();
        $this->eventObserver->expects($this->any())->method('getEvent')->will($this->returnValue($this->event));
        $this->assertInstanceOf(
            '\Magento\Banner\Model\Observer',
            $this->observer->prepareSalesRuleSave($this->eventObserver)
        );
    }

    public function testBindRelatedBannersDataProvider()
    {
        return [
            [
                []
            ],
            [
                'banner1',
                'banner2'
            ]
        ];
    }
}

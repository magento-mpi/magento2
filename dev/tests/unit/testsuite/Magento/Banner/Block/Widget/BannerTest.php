<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Banner\Block\Widget;

class BannerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Banner\Block\Widget\Banner
     */
    private $_block;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_bannerResource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_checkoutSession;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $httpContext;

    /**
     * @var array
     */
    private $_fixtureBanners = [456 => '[Banner 456]', 789 => '[Banner 789]'];

    protected function setUp()
    {
        $this->_bannerResource = $this->getMock('Magento\Banner\Model\Resource\Banner', [], [], '', false);

        $this->_checkoutSession = $this->getMock(
            'Magento\Checkout\Model\Session',
            ['getQuoteId', 'getQuote'],
            [],
            '',
            false
        );

        $this->httpContext = $this->getMock(
            '\Magento\Framework\App\Http\Context',
            ['getValue'],
            [],
            '',
            false
        );
        $this->httpContext->expects($this->any())->method('getValue')->will($this->returnValue(4));

        $pageFilterMock = $this->getMock('Magento\Cms\Model\Template\Filter', [], [], '', false);
        $pageFilterMock->expects($this->any())->method('filter')->will($this->returnArgument(0));
        $filterProviderMock = $this->getMock('Magento\Cms\Model\Template\FilterProvider', [], [], '', false);
        $filterProviderMock->expects($this->any())->method('getPageFilter')->will($this->returnValue($pageFilterMock));

        $currentStore = new \Magento\Framework\Object(['id' => 42]);
        $currentWebsite = new \Magento\Framework\Object(['id' => 57]);
        $storeManager = $this->getMockForAbstractClass(
            'Magento\Store\Model\StoreManagerInterface',
            [],
            '',
            true,
            true,
            true,
            ['getStore', 'getWebsite']
        );
        $storeManager->expects($this->once())->method('getStore')->will($this->returnValue($currentStore));
        $storeManager->expects($this->once())->method('getWebsite')->will($this->returnValue($currentWebsite));


        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_block = $helper->getObject(
            'Magento\Banner\Block\Widget\Banner',
            [
                'resource' => $this->_bannerResource,
                'checkoutSession' => $this->_checkoutSession,
                'httpContext' => $this->httpContext,
                'filterProvider' => $filterProviderMock,
                'storeManager' => $storeManager,
                'data' => [
                    'types' => ['footer', 'header'],
                    'rotate' => \Magento\Banner\Block\Widget\Banner::BANNER_WIDGET_RORATE_NONE
                ]
            ]
        );
    }

    public function testGetBannersContentFixed()
    {
        $this->_block->addData(
            [
                'banner_ids' => '-123,456,789',
                'display_mode',
                \Magento\Banner\Block\Widget\Banner::BANNER_WIDGET_DISPLAY_FIXED
            ]
        );

        $this->_bannerResource->expects($this->at(0))->method('filterByTypes')->with(['footer', 'header']);

        $this->_bannerResource->expects(
            $this->at(1)
        )->method(
            'getExistingBannerIdsBySpecifiedIds'
        )->with(
            [-123, 456, 789]
        )->will(
            $this->returnValue([456, 789])
        );
        $this->_bannerResource->expects(
            $this->at(2)
        )->method(
            'getBannersContent'
        )->with(
            [456, 789],
            42
        )->will(
            $this->returnValue($this->_fixtureBanners)
        );
        $this->_bannerResource->expects($this->at(3))->method('filterByTypes')->with([]);

        $this->assertEquals($this->_fixtureBanners, $this->_block->getBannersContent());
    }

    public function testGetBannersContentCatalogRule()
    {
        $this->_block->addData(
            ['display_mode' => \Magento\Banner\Block\Widget\Banner::BANNER_WIDGET_DISPLAY_CATALOGRULE]
        );

        $this->_bannerResource->expects($this->at(0))->method('filterByTypes')->with(['footer', 'header']);
        $this->_bannerResource->expects(
            $this->at(1)
        )->method(
            'getCatalogRuleRelatedBannerIds'
        )->with(
            57,
            4
        )->will(
            $this->returnValue([456, 789])
        );
        $this->_bannerResource->expects(
            $this->at(2)
        )->method(
            'getBannersContent'
        )->with(
            [456, 789],
            42
        )->will(
            $this->returnValue($this->_fixtureBanners)
        );
        $this->_bannerResource->expects($this->at(3))->method('filterByTypes')->with([]);

        $this->assertEquals($this->_fixtureBanners, $this->_block->getBannersContent());
    }

    public function testGetBannersContentSalesRule()
    {
        $this->_block->addData(
            ['display_mode' => \Magento\Banner\Block\Widget\Banner::BANNER_WIDGET_DISPLAY_SALESRULE]
        );

        $quote = new \Magento\Framework\Object(['applied_rule_ids' => '15,11,12']);
        $this->_checkoutSession->expects($this->once())->method('getQuoteId')->will($this->returnValue(8000));
        $this->_checkoutSession->expects($this->once())->method('getQuote')->will($this->returnValue($quote));

        $this->_bannerResource->expects($this->at(0))->method('filterByTypes')->with(['footer', 'header']);
        $this->_bannerResource->expects(
            $this->at(1)
        )->method(
            'getSalesRuleRelatedBannerIds'
        )->with(
            [15, 11, 12]
        )->will(
            $this->returnValue([456, 789])
        );
        $this->_bannerResource->expects(
            $this->at(2)
        )->method(
            'getBannersContent'
        )->with(
            [456, 789],
            42
        )->will(
            $this->returnValue($this->_fixtureBanners)
        );
        $this->_bannerResource->expects($this->at(3))->method('filterByTypes')->with([]);

        $this->assertEquals($this->_fixtureBanners, $this->_block->getBannersContent());
    }

    public function testGetIdentities()
    {
        $this->_block->setBannerIds([1, 2]);
        $this->assertEquals(
            [\Magento\Banner\Model\Banner::CACHE_TAG . '_1', \Magento\Banner\Model\Banner::CACHE_TAG . '_2'],
            $this->_block->getIdentities()
        );
    }
}

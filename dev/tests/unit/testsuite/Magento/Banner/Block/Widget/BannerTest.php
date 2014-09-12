<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
    private $_fixtureBanners = array(456 => '[Banner 456]', 789 => '[Banner 789]');

    protected function setUp()
    {
        $this->_bannerResource = $this->getMock('Magento\Banner\Model\Resource\Banner', array(), array(), '', false);

        $this->_checkoutSession = $this->getMock(
            'Magento\Checkout\Model\Session',
            array('getQuoteId', 'getQuote'),
            array(),
            '',
            false
        );

        $this->httpContext = $this->getMock(
            '\Magento\Framework\App\Http\Context',
            array('getValue'),
            array(),
            '',
            false
        );
        $this->httpContext->expects($this->any())->method('getValue')->will($this->returnValue(4));

        $pageFilterMock = $this->getMock('Magento\Cms\Model\Template\Filter', array(), array(), '', false);
        $pageFilterMock->expects($this->any())->method('filter')->will($this->returnArgument(0));
        $filterProviderMock = $this->getMock('Magento\Cms\Model\Template\FilterProvider', array(), array(), '', false);
        $filterProviderMock->expects($this->any())->method('getPageFilter')->will($this->returnValue($pageFilterMock));

        $currentStore = new \Magento\Framework\Object(array('id' => 42));
        $currentWebsite = new \Magento\Framework\Object(array('id' => 57));
        $storeManager = $this->getMockForAbstractClass(
            'Magento\Framework\StoreManagerInterface',
            array(),
            '',
            true,
            true,
            true,
            array('getStore', 'getWebsite')
        );
        $storeManager->expects($this->once())->method('getStore')->will($this->returnValue($currentStore));
        $storeManager->expects($this->once())->method('getWebsite')->will($this->returnValue($currentWebsite));


        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_block = $helper->getObject(
            'Magento\Banner\Block\Widget\Banner',
            array(
                'resource' => $this->_bannerResource,
                'checkoutSession' => $this->_checkoutSession,
                'httpContext' => $this->httpContext,
                'filterProvider' => $filterProviderMock,
                'storeManager' => $storeManager,
                'data' => array(
                    'types' => array('footer', 'header'),
                    'rotate' => \Magento\Banner\Block\Widget\Banner::BANNER_WIDGET_RORATE_NONE
                )
            )
        );
    }

    public function testGetBannersContentFixed()
    {
        $this->_block->addData(
            array(
                'banner_ids' => '-123,456,789',
                'display_mode',
                \Magento\Banner\Block\Widget\Banner::BANNER_WIDGET_DISPLAY_FIXED
            )
        );

        $this->_bannerResource->expects($this->at(0))->method('filterByTypes')->with(array('footer', 'header'));

        $this->_bannerResource->expects(
            $this->at(1)
        )->method(
            'getExistingBannerIdsBySpecifiedIds'
        )->with(
            array(-123, 456, 789)
        )->will(
            $this->returnValue(array(456, 789))
        );
        $this->_bannerResource->expects(
            $this->at(2)
        )->method(
            'getBannersContent'
        )->with(
            array(456, 789),
            42
        )->will(
            $this->returnValue($this->_fixtureBanners)
        );
        $this->_bannerResource->expects($this->at(3))->method('filterByTypes')->with(array());

        $this->assertEquals($this->_fixtureBanners, $this->_block->getBannersContent());
    }

    public function testGetBannersContentCatalogRule()
    {
        $this->_block->addData(
            array('display_mode' => \Magento\Banner\Block\Widget\Banner::BANNER_WIDGET_DISPLAY_CATALOGRULE)
        );

        $this->_bannerResource->expects($this->at(0))->method('filterByTypes')->with(array('footer', 'header'));
        $this->_bannerResource->expects(
            $this->at(1)
        )->method(
            'getCatalogRuleRelatedBannerIds'
        )->with(
            57,
            4
        )->will(
            $this->returnValue(array(456, 789))
        );
        $this->_bannerResource->expects(
            $this->at(2)
        )->method(
            'getBannersContent'
        )->with(
            array(456, 789),
            42
        )->will(
            $this->returnValue($this->_fixtureBanners)
        );
        $this->_bannerResource->expects($this->at(3))->method('filterByTypes')->with(array());

        $this->assertEquals($this->_fixtureBanners, $this->_block->getBannersContent());
    }

    public function testGetBannersContentSalesRule()
    {
        $this->_block->addData(
            array('display_mode' => \Magento\Banner\Block\Widget\Banner::BANNER_WIDGET_DISPLAY_SALESRULE)
        );

        $quote = new \Magento\Framework\Object(array('applied_rule_ids' => '15,11,12'));
        $this->_checkoutSession->expects($this->once())->method('getQuoteId')->will($this->returnValue(8000));
        $this->_checkoutSession->expects($this->once())->method('getQuote')->will($this->returnValue($quote));

        $this->_bannerResource->expects($this->at(0))->method('filterByTypes')->with(array('footer', 'header'));
        $this->_bannerResource->expects(
            $this->at(1)
        )->method(
            'getSalesRuleRelatedBannerIds'
        )->with(
            array(15, 11, 12)
        )->will(
            $this->returnValue(array(456, 789))
        );
        $this->_bannerResource->expects(
            $this->at(2)
        )->method(
            'getBannersContent'
        )->with(
            array(456, 789),
            42
        )->will(
            $this->returnValue($this->_fixtureBanners)
        );
        $this->_bannerResource->expects($this->at(3))->method('filterByTypes')->with(array());

        $this->assertEquals($this->_fixtureBanners, $this->_block->getBannersContent());
    }

    public function testGetIdentities()
    {
        $this->_block->setBannerIds(array(1, 2));
        $this->assertEquals(
            array(\Magento\Banner\Model\Banner::CACHE_TAG . '_1', \Magento\Banner\Model\Banner::CACHE_TAG . '_2'),
            $this->_block->getIdentities()
        );
    }
}

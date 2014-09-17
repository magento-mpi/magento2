<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Block\Rss;

use \Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class DiscountsTest
 * @package Magento\SalesRule\Block\Rss
 */
class DiscountsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\SalesRule\Block\Rss\Discounts
     */
    protected $block;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Framework\View\Element\Template\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerInterface;

    /**
     * @var \Magento\Framework\App\Http\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context2;

    /**
     * @var \Magento\SalesRule\Model\Rss\Discounts|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $discounts;

    /**
     * @var \Magento\Framework\App\Rss\UrlBuilderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilderInterface;

    protected function setUp()
    {
        $this->context = $this->getMock('Magento\Framework\View\Element\Template\Context', [], [], '', false);
        $this->storeManagerInterface = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $this->context2 = $this->getMock('Magento\Framework\App\Http\Context');
        $this->discounts = $this->getMock('Magento\SalesRule\Model\Rss\Discounts', [], [], '', false);
        $this->urlBuilderInterface = $this->getMock('Magento\Framework\App\Rss\UrlBuilderInterface');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->block = $this->objectManagerHelper->getObject(
            'Magento\SalesRule\Block\Rss\Discounts',
            [
                'context' => $this->context,
                'storeManager' => $this->storeManagerInterface,
                'httpContext' => $this->context2,
                'rssModel' => $this->discounts,
                'rssUrlBuilder' => $this->urlBuilderInterface
            ]
        );
    }

    public function testGetRssData()
    {
    }

    public function testGetCacheLifetime()
    {
    }

    public function testIsAllowed()
    {
    }

    public function testGetFeeds()
    {
    }
}

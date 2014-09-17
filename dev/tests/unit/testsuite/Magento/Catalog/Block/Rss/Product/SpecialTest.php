<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Rss\Product;

use \Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class SpecialTest
 * @package Magento\Catalog\Block\Rss\Product
 */
class SpecialTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Block\Rss\Product\Special
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
     * @var \Magento\Framework\App\Http\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context2;

    /**
     * @var \Magento\Catalog\Helper\Image|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogImageHelper;

    /**
     * @var \Magento\Catalog\Helper\Output|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogOutputHelper;

    /**
     * @var \Magento\Catalog\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogHelper;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceCurrencyInterface;

    /**
     * @var \Magento\Catalog\Model\Rss\Product\Special|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $special;

    /**
     * @var \Magento\Framework\App\Rss\UrlBuilderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilderInterface;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerInterface;

    protected function setUp()
    {
        $this->context = $this->getMock('Magento\Framework\View\Element\Template\Context', [], [], '', false);
        $this->context2 = $this->getMock('Magento\Framework\App\Http\Context');
        $this->catalogImageHelper = $this->getMock('Magento\Catalog\Helper\Image', [], [], '', false);
        $this->catalogOutputHelper = $this->getMock('Magento\Catalog\Helper\Output', [], [], '', false);
        $this->catalogHelper = $this->getMock('Magento\Catalog\Helper\Data', [], [], '', false);
        $this->priceCurrencyInterface = $this->getMock('Magento\Framework\Pricing\PriceCurrencyInterface');
        $this->special = $this->getMock('Magento\Catalog\Model\Rss\Product\Special', [], [], '', false);
        $this->urlBuilderInterface = $this->getMock('Magento\Framework\App\Rss\UrlBuilderInterface');
        $this->storeManagerInterface = $this->getMock('Magento\Store\Model\StoreManagerInterface');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->block = $this->objectManagerHelper->getObject(
            'Magento\Catalog\Block\Rss\Product\Special',
            [
                'context' => $this->context,
                'httpContext' => $this->context2,
                'imageHelper' => $this->catalogImageHelper,
                'outputHelper' => $this->catalogOutputHelper,
                'catalogHelper' => $this->catalogHelper,
                'priceCurrency' => $this->priceCurrencyInterface,
                'rssModel' => $this->special,
                'rssUrlBuilder' => $this->urlBuilderInterface,
                'storeManager' => $this->storeManagerInterface
            ]
        );
    }

    public function testGetRssData()
    {
    }

    public function testIsAllowed()
    {
    }

    public function testGetCacheLifetime()
    {
    }

    public function testGetFeeds()
    {
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Rss;

use \Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class CategoryTest
 * @package Magento\Catalog\Block\Rss
 */
class CategoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Block\Rss\Category
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
     * @var \Magento\Catalog\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Catalog\Model\Rss\Category|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $category;

    /**
     * @var \Magento\Framework\App\Rss\UrlBuilderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilderInterface;

    /**
     * @var \Magento\Catalog\Helper\Image|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogImageHelper;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $session;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerInterface;

    protected function setUp()
    {
        $this->context = $this->getMock('Magento\Framework\View\Element\Template\Context', [], [], '', false);
        $this->context2 = $this->getMock('Magento\Framework\App\Http\Context');
        $this->catalogHelper = $this->getMock('Magento\Catalog\Helper\Data', [], [], '', false);
        $this->categoryFactory = $this->getMock('Magento\Catalog\Model\CategoryFactory');
        $this->category = $this->getMock('Magento\Catalog\Model\Rss\Category', [], [], '', false);
        $this->urlBuilderInterface = $this->getMock('Magento\Framework\App\Rss\UrlBuilderInterface');
        $this->catalogImageHelper = $this->getMock('Magento\Catalog\Helper\Image', [], [], '', false);
        $this->session = $this->getMock('Magento\Customer\Model\Session', [], [], '', false);
        $this->storeManagerInterface = $this->getMock('Magento\Store\Model\StoreManagerInterface');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->block = $this->objectManagerHelper->getObject(
            'Magento\Catalog\Block\Rss\Category',
            [
                'context' => $this->context,
                'httpContext' => $this->context2,
                'catalogData' => $this->catalogHelper,
                'categoryFactory' => $this->categoryFactory,
                'rssModel' => $this->category,
                'rssUrlBuilder' => $this->urlBuilderInterface,
                'imageHelper' => $this->catalogImageHelper,
                'customerSession' => $this->session,
                'storeManager' => $this->storeManagerInterface
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

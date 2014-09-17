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
 * Class NewProductsTest
 * @package Magento\Catalog\Block\Rss\Product
 */
class NewProductsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Block\Rss\Product\NewProducts
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
     * @var \Magento\Catalog\Helper\Image|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogImageHelper;

    /**
     * @var \Magento\Catalog\Model\Rss\Product\NewProducts|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $newProducts;

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
        $this->catalogImageHelper = $this->getMock('Magento\Catalog\Helper\Image', [], [], '', false);
        $this->newProducts = $this->getMock('Magento\Catalog\Model\Rss\Product\NewProducts', [], [], '', false);
        $this->urlBuilderInterface = $this->getMock('Magento\Framework\App\Rss\UrlBuilderInterface');
        $this->storeManagerInterface = $this->getMock('Magento\Store\Model\StoreManagerInterface');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->block = $this->objectManagerHelper->getObject(
            'Magento\Catalog\Block\Rss\Product\NewProducts',
            [
                'context' => $this->context,
                'imageHelper' => $this->catalogImageHelper,
                'rssModel' => $this->newProducts,
                'rssUrlBuilder' => $this->urlBuilderInterface,
                'storeManager' => $this->storeManagerInterface
            ]
        );
    }

    public function testIsAllowed()
    {
    }

    public function testGetRssData()
    {
    }

    public function testGetCacheLifetime()
    {
    }

    public function testGetFeeds()
    {
    }
}

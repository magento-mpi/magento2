<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Adminhtml\Rss;

use \Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class NotifyStockTest
 * @package Magento\Catalog\Block\Adminhtml\Rss
 */
class NotifyStockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Block\Adminhtml\Rss\NotifyStock
     */
    protected $block;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Backend\Block\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\Catalog\Model\Rss\Product\NotifyStock|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $notifyStock;

    /**
     * @var \Magento\Framework\App\Rss\UrlBuilderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilderInterface;

    protected function setUp()
    {
        $this->context = $this->getMock('Magento\Backend\Block\Context', [], [], '', false);
        $this->notifyStock = $this->getMock('Magento\Catalog\Model\Rss\Product\NotifyStock', [], [], '', false);
        $this->urlBuilderInterface = $this->getMock('Magento\Framework\App\Rss\UrlBuilderInterface');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->block = $this->objectManagerHelper->getObject(
            'Magento\Catalog\Block\Adminhtml\Rss\NotifyStock',
            [
                'context' => $this->context,
                'rssModel' => $this->notifyStock,
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

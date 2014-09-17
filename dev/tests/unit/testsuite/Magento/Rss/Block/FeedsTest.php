<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rss\Block;

use \Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class FeedsTest
 * @package Magento\Rss\Block
 */
class FeedsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rss\Block\Feeds
     */
    protected $feeds;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Framework\View\Element\Template\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\Framework\App\Rss\RssManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rssManagerInterface;

    protected function setUp()
    {
        $this->context = $this->getMock('Magento\Framework\View\Element\Template\Context', [], [], '', false);
        $this->rssManagerInterface = $this->getMock('Magento\Framework\App\Rss\RssManagerInterface');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->feeds = $this->objectManagerHelper->getObject(
            'Magento\Rss\Block\Feeds',
            [
                'context' => $this->context,
                'rssManager' => $this->rssManagerInterface
            ]
        );
    }

    public function testGetFeeds()
    {
    }
}

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
     * @var \Magento\Framework\App\Rss\RssManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rssManagerInterface;

    protected function setUp()
    {
        $this->context = $this->getMock('Magento\Framework\View\Element\Template\Context', [], [], '', false);
        $this->rssManagerInterface = $this->getMock('Magento\Framework\App\Rss\RssManagerInterface');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->block = $this->objectManagerHelper->getObject(
            'Magento\Rss\Block\Feeds',
            [
                'context' => $this->context,
                'rssManager' => $this->rssManagerInterface
            ]
        );
    }

    public function testGetFeeds()
    {
        $provider1 = $this->getMock('\Magento\Framework\App\Rss\DataProviderInterface');
        $provider2 = $this->getMock('\Magento\Framework\App\Rss\DataProviderInterface');
        $feed1 = array(
            'group' => 'Some Group',
            'feeds' => array(
                array('link' => 'feed 1 link', 'label' => 'Feed 1 Label')
            )
        );
        $feed2 = array('link' => 'feed 2 link', 'label' => 'Feed 2 Label');
        $provider1->expects($this->once())->method('getFeeds')->will($this->returnValue($feed1));
        $provider2->expects($this->once())->method('getFeeds')->will($this->returnValue($feed2));
        $this->rssManagerInterface->expects($this->once())->method('getProviders')
            ->will($this->returnValue(array($provider1, $provider2)));

        $this->assertEquals(array($feed2, $feed1), $this->block->getFeeds());
    }
}

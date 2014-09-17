<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Block\Adminhtml;

use \Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class RssTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Review\Block\Adminhtml\Rss
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
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerInterface;

    /**
     * @var \Magento\Review\Model\Rss|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rss;

    protected function setUp()
    {
        $this->context = $this->getMock('Magento\Backend\Block\Context', [], [], '', false);
        $this->storeManagerInterface = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $this->rss = $this->getMock('Magento\Review\Model\Rss', [], [], '', false);

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->block = $this->objectManagerHelper->getObject(
            'Magento\Review\Block\Adminhtml\Rss',
            [
                'context' => $this->context,
                'storeManager' => $this->storeManagerInterface,
                'rssModel' => $this->rss
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

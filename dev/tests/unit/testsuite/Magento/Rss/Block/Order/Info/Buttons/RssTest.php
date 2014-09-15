<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rss\Block\Order\Info\Buttons;

use \Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class RssTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Rss\Block\Order\Info\Buttons\Rss */
    protected $rss;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Framework\View\Element\Template\Context|\PHPUnit_Framework_MockObject_MockObject */
    protected $context;

    /** @var \Magento\Framework\Registry */
    protected $registry;

    /** @var \Magento\Rss\Helper\Order|\PHPUnit_Framework_MockObject_MockObject */
    protected $rssOrderHelper;

    protected function setUp()
    {
        $this->context = $this->getMock('Magento\Framework\View\Element\Template\Context', [], [], '', false);
        $this->rssOrderHelper = $this->getMock('Magento\Rss\Helper\Order', [], [], '', false);
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->registry = $this->objectManagerHelper->getObject('Magento\Framework\Registry');

        $this->rss = $this->objectManagerHelper->getObject(
            'Magento\Rss\Block\Order\Info\Buttons\Rss',
            [
                'context' => $this->context,
                'registry' => $this->registry,
                'orderHelper' => $this->rssOrderHelper
            ]
        );
    }

    public function testGetOrder()
    {
        $currentOrder = $this->getMock('Magento\Sales\Model\Order', [], [], '', false);
        $this->registry->register('current_order', $currentOrder);
        $this->assertEquals($currentOrder, $this->rss->getOrder());
    }

    public function testGetOrderHelper()
    {
        $orderHelper = $this->rss->getOrderHelper();
        $this->assertEquals($this->rssOrderHelper, $orderHelper);
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Block\Customer;

class AccountLinkTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->_objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    public function testToHtml()
    {
        /** @var \Magento\Reward\Helper\Data|\PHPUnit_Framework_MockObject_MockObject $helper */
        $helper = $this->getMockBuilder('Magento\Reward\Helper\Data')->disableOriginalConstructor()->getMock();

        /** @var \Magento\Reward\Block\Customer\AccountLink $block */
        $block = $this->_objectManagerHelper->getObject(
            'Magento\Reward\Block\Customer\AccountLink',
            ['rewardHelper' => $helper]
        );

        $helper->expects($this->once())->method('isEnabledOnFront')->will($this->returnValue(false));

        $this->assertEquals('', $block->toHtml());
    }
}

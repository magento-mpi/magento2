<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Reward_Block_Customer_AccountLinkTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->_objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
    }

    public function testToHtml()
    {
        /** @var \Magento\Reward\Helper\Data|PHPUnit_Framework_MockObject_MockObject $helper */
        $helper =
            $this->getMockBuilder('Magento\Reward\Helper\Data')->disableOriginalConstructor()->getMock();

        /** @var \Magento\Reward\Block\Customer\AccountLink $block */
        $block = $this->_objectManagerHelper->getObject(
            'Magento\Reward\Block\Customer\AccountLink',
            array(
                'rewardHelper' => $helper,
            )
        );

        $helper->expects($this->once())->method('isEnabledOnFront')->will(
            $this->returnValue(false)
        );

        $this->assertEquals('', $block->toHtml());
    }
} 

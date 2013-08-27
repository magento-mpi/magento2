<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Reward_Block_Customer_AccountLinkTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->_objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
    }

    public function testToHtml()
    {
        /** @var Enterprise_Reward_Helper_Data|PHPUnit_Framework_MockObject_MockObject $helper */
        $helper =
            $this->getMockBuilder('Enterprise_Reward_Helper_Data')->disableOriginalConstructor()->getMock();

        /** @var Enterprise_Reward_Block_Customer_AccountLink $block */
        $block = $this->_objectManagerHelper->getObject(
            'Enterprise_Reward_Block_Customer_AccountLink',
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
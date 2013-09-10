<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_CustomerCustomAttributes_Block_Account_RegisterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TestFramework_Helper_ObjectManager
     */
    protected $_objectManagerHelper;

    protected function setUp()
    {
        $this->_objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
    }

    public function testToHtml()
    {
        /** @var Magento_AdvancedCheckout_Helper_Data|PHPUnit_Framework_MockObject_MockObject $customerHelper */
        $customerHelper = $this->getMockBuilder('Magento_AdvancedCheckout_Helper_Data')
            ->disableOriginalConstructor()->getMock();

        /** @var Magento_Invitation_Block_Link $block */
        $block = $this->_objectManagerHelper->getObject(
            'Magento_AdvancedCheckout_Block_Customer_Link',
            array(
                'customerHelper' => $customerHelper,
            )
        );

        $customerHelper->expects($this->once())->method('isSkuApplied')->will(
            $this->returnValue(false)
        );

        $this->assertEquals('', $block->toHtml());
    }
}
 
<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Checkout_Block_Customer_LinkTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_Helper_ObjectManager
     */
    protected $_objectManagerHelper;

    protected function setUp()
    {
        $this->_objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
    }

    public function testToHtml()
    {
        /** @var Enterprise_Checkout_Helper_Data|PHPUnit_Framework_MockObject_MockObject $customerHelper */
        $customerHelper = $this->getMockBuilder('Enterprise_Checkout_Helper_Data')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var Enterprise_Invitation_Block_Link $block */
        $block = $this->_objectManagerHelper->getObject(
            'Enterprise_Checkout_Block_Customer_Link',
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
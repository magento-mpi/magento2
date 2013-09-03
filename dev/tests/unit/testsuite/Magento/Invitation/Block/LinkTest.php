<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Invitation_Block_LinkTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_Helper_ObjectManager
     */
    protected $_objectManagerHelper;

    protected function setUp()
    {
        $this->_objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
    }

    public function testGetHref()
    {
        $url = 'http://test.exmaple.com/test';

        $invitationHelper = $this->getMockBuilder('Magento_Invitation_Helper_Data')
            ->disableOriginalConstructor()
            ->getMock();

        $invitationHelper->expects($this->once())->method('getCustomerInvitationFormUrl')->will(
            $this->returnValue($url)
        );

        $block = $this->_objectManagerHelper->getObject(
            'Magento_Invitation_Block_Link',
            array(
                'invitationHelper' => $invitationHelper,
            )
        );
        $this->assertEquals($url, $block->getHref());
    }

    /**
     * @return array
     */
    public static function dataForToHtmlTest()
    {
        return array(
            array(true, false),
            array(false, true),
            array(false, false),
        );
    }

    /**
     * @dataProvider dataForToHtmlTest
     * @param bool $isLoggedIn
     * @param bool $isEnabledOnFront
     */
    public function testToHtml($isLoggedIn, $isEnabledOnFront)
    {
        /** @var Magento_Customer_Model_Session $customerSession |PHPUnit_Framework_MockObject_MockObject */
        $customerSession = $this->getMockBuilder('Magento_Customer_Model_Session')
            ->disableOriginalConstructor()->getMock();

        /** @var Magento_Invitation_Model_Config $invitationConfiguration |PHPUnit_Framework_MockObject_MockObject */
        $invitationConfiguration = $this->getMockBuilder('Magento_Invitation_Model_Config')
            ->disableOriginalConstructor()->getMock();

        /** @var Magento_Invitation_Block_Link $block */
        $block = $this->_objectManagerHelper->getObject(
            'Magento_Invitation_Block_Link',
            array(
                'customerSession' => $customerSession,
                'invitationConfiguration' => $invitationConfiguration,
            )
        );

        $customerSession->expects($this->any())->method('isLoggedIn')->will(
            $this->returnValue($isLoggedIn)
        );

        $invitationConfiguration->expects($this->any())->method('isEnabledOnFront')->will(
            $this->returnValue($isEnabledOnFront)
        );

        $this->assertEquals('', $block->toHtml());
    }
}
 
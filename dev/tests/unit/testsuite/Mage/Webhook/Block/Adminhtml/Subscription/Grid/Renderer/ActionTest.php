<?php
/**
 * Mage_Webhook_Block_Adminhtml_Subscription_Grid_Renderer_Action
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Block_Adminhtml_Subscription_Grid_Renderer_ActionTest extends PHPUnit_Framework_TestCase
{
    public function testRenderWrongType()
    {
        $context = $this->getMockBuilder('Mage_Backend_Block_Context')
            ->disableOriginalConstructor()
            ->getMock();
        $gridRenderer = new Mage_Webhook_Block_Adminhtml_Subscription_Grid_Renderer_Action($context);
        $row = $this->getMockBuilder('Varien_Object')
            ->disableOriginalConstructor()
            ->getMock();

        $renderedRow = $gridRenderer->render($row);

        $this->assertEquals('', $renderedRow);
    }

    /**
     * @dataProvider renderDataProvider
     * @param int $status
     * @param string $contains
     */
    public function testRender($status, $contains)
    {
        $urlBuilder = $this->getMock('Mage_Core_Model_Url', array('getUrl'), array(), '', false);
        $urlBuilder->expects($this->any())
            ->method('getUrl')
            ->will($this->returnArgument(0));
        $translator = $this->getMock('Mage_Core_Model_Translate', array('translate'), array(), '', false);
        $context = $this->getMockBuilder('Mage_Backend_Block_Context')
            ->disableOriginalConstructor()
            ->getMock();
        $context->expects($this->once())
            ->method('getUrlBuilder')
            ->will($this->returnValue($urlBuilder));
        $context->expects($this->any())
            ->method('getTranslator')
            ->will($this->returnValue($translator));
        $gridRenderer = new Mage_Webhook_Block_Adminhtml_Subscription_Grid_Renderer_Action($context);
        $row = $this->getMockBuilder('Mage_Webhook_Model_Subscription')
            ->disableOriginalConstructor()
            ->getMock();
        $row->expects($this->any())
            ->method('getStatus')
            ->will($this->returnValue($status));
        $row->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(42));

        $renderedRow = $gridRenderer->render($row);

        $this->assertFalse(false === strpos($renderedRow, '<a href="'), $renderedRow);
        $this->assertFalse(false === strpos($renderedRow, $contains), $renderedRow);
        $this->assertFalse(false === strpos($renderedRow, '</a>'), $renderedRow);
    }

    /**
     * Data provider for our testRender()
     *
     * @return array
     */
    public function renderDataProvider()
    {
        return array(
            array(Mage_Webhook_Model_Subscription::STATUS_ACTIVE, 'revoke'),
            array(Mage_Webhook_Model_Subscription::STATUS_REVOKED, 'activate'),
            array(Mage_Webhook_Model_Subscription::STATUS_INACTIVE, 'activate'),
        );
    }
}

<?php
/**
 * \Magento\Webhook\Block\Adminhtml\Subscription\Grid\Renderer\Action
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Block\Adminhtml\Subscription\Grid\Renderer;

class ActionTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderWrongType()
    {
        $context = $this->getMockBuilder('Magento\Backend\Block\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $gridRenderer = new \Magento\Webhook\Block\Adminhtml\Subscription\Grid\Renderer\Action($context);
        $row = $this->getMockBuilder('Magento\Object')
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
        $urlBuilder = $this->getMock('Magento\Core\Model\Url', array('getUrl'), array(), '', false);
        $urlBuilder->expects($this->any())
            ->method('getUrl')
            ->will($this->returnArgument(0));
        $translator = $this->getMock('Magento\Core\Model\Translate', array('translate'), array(), '', false);
        $context = $this->getMockBuilder('Magento\Backend\Block\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $context->expects($this->once())
            ->method('getUrlBuilder')
            ->will($this->returnValue($urlBuilder));
        $context->expects($this->any())
            ->method('getTranslator')
            ->will($this->returnValue($translator));
        $gridRenderer = new \Magento\Webhook\Block\Adminhtml\Subscription\Grid\Renderer\Action($context);
        $row = $this->getMockBuilder('Magento\Webhook\Model\Subscription')
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
            array(\Magento\Webhook\Model\Subscription::STATUS_ACTIVE, 'revoke'),
            array(\Magento\Webhook\Model\Subscription::STATUS_REVOKED, 'activate'),
            array(\Magento\Webhook\Model\Subscription::STATUS_INACTIVE, 'activate'),
        );
    }
}

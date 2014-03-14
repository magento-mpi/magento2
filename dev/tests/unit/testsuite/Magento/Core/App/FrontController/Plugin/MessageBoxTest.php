<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\App\FrontController\Plugin;

/**
 * Class MessageBoxTest
  */
class MessageBoxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Version instance
     *
     * @var MessageBox
     */
    protected $msgBox;

    /**
     * @var \Magento\PageCache\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * Cookie mock
     *
     * @var \Magento\Stdlib\Cookie|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cookieMock;

    /**
     * Request mock
     *
     * @var \Magento\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * Create cookie and request mock, version instance
     */
    public function setUp()
    {
        $this->cookieMock = $this->getMock('Magento\Stdlib\Cookie', array('set', 'get'), array(), '', false);
        $this->requestMock = $this->getMock('Magento\App\Request\Http', array('isPost'), array(), '', false);
        $this->configMock = $this->getMock('Magento\PageCache\Model\Config', array('isEnabled'), array(), '', false);
        $this->msgBox =  new MessageBox($this->cookieMock, $this->requestMock, $this->configMock);
    }

    /**
     * Handle private content message box cookie
     * Set cookie if it is not set.
     * Set or unset cookie on post request
     * In all other cases do nothing.
     */
    public function testProcess()
    {
        $this->requestMock->expects($this->once())
            ->method('isPost')
            ->will($this->returnValue(true));
        $this->cookieMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo(\Magento\App\PageCache\MessageBox::COOKIE_NAME))
            ->will($this->returnValue(false));
        $this->cookieMock->expects($this->once())
            ->method('set')
            ->with(
                $this->equalTo(\Magento\App\PageCache\MessageBox::COOKIE_NAME), 1,
                $this->equalTo(\Magento\App\PageCache\MessageBox::COOKIE_PERIOD)
            );
        $this->msgBox->process();
    }

    /**
     * Test case for unsetting cookie
     */
    public function testProcessUnsetCookie()
    {
        $this->cookieMock->set(\Magento\App\PageCache\MessageBox::COOKIE_NAME, 0);
        $this->requestMock->expects($this->once())
            ->method('isPost')
            ->will($this->returnValue(true));
        $this->cookieMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo(\Magento\App\PageCache\MessageBox::COOKIE_NAME))
            ->will($this->returnValue(0));
        $this->cookieMock->expects($this->once())
            ->method('set')
            ->with(
                $this->equalTo(\Magento\App\PageCache\MessageBox::COOKIE_NAME),
                $this->equalTo(null), 0
            );
        $this->msgBox->process();
    }

    /**
     * IF request is not POST
     */
    public function testProcessNoPost()
    {
        $this->requestMock->expects($this->once())
            ->method('isPost')
            ->will($this->returnValue(false));
        $this->msgBox->process();
    }
}

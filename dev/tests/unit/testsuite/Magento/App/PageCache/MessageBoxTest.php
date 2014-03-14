<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\PageCache;

class MessageBoxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Version instance
     *
     * @var MessageBox
     */
    protected $msgBox;

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
        $this->msgBox =  new MessageBox($this->cookieMock, $this->requestMock);
    }

    /**
     * Handle private content message box cookie
     * Set cookie if it is not set.
     * Set or unset cookie on post request
     * In all other cases do nothing.
     *
     * @dataProvider processProvider
     * @param bool $isPost
     * @param int $cookie
     */
    public function testProcess($isPost, $cookie)
    {

        $this->requestMock->expects($this->once())
            ->method('isPost')
            ->will($this->returnValue($isPost));
        if ($isPost) {
            $this->cookieMock->expects($this->once())
                ->method('get')
                ->with($this->equalTo(\Magento\App\PageCache\MessageBox::COOKIE_NAME))
                ->will($this->returnValue($cookie));
            if ($cookie) {
                $this->cookieMock->expects($this->once())
                    ->method('set')
                    ->with(
                        $this->equalTo(\Magento\App\PageCache\MessageBox::COOKIE_NAME),
                        $this->equalTo($cookie), 0
                    );
            } else {
                $this->cookieMock->expects($this->once())
                    ->method('set')
                    ->with(
                        $this->equalTo(\Magento\App\PageCache\MessageBox::COOKIE_NAME), 1,
                        $this->equalTo(\Magento\App\PageCache\MessageBox::COOKIE_PERIOD)
                    );
            }
        }
        $this->msgBox->process();
    }

    /**
     * Data provider for testProcess
     * @return array
     */
    public function processProvider()
    {
        return [
            'post, cookie is not set' => [true, null],
            'post, cookie is set' => [true, 0],
            'no post, no cookie' => [false, null]
        ];
    }
} 
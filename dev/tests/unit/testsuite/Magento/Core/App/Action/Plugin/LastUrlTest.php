<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\App\Action\Plugin;

class LastUrlTest extends \PHPUnit_Framework_TestCase
{
    public function testAfterDispatch()
    {
        $session = $this->getMock('\Magento\Core\Model\Session', array('setLastUrl'), array(), '', false);
        $subjectMock = $this->getMock('Magento\App\Action\Action', array(), array(), '', false);
        $closureMock = function () {
            return 'result';
        };
        $requestMock = $this->getMock('Magento\App\RequestInterface');
        $url = $this->getMock('\Magento\Url', array(), array(), '', false);
        $plugin = new \Magento\Core\App\Action\Plugin\LastUrl($session, $url);
        $session->expects($this->once())->method('setLastUrl')->with('http://example.com');
        $url->expects($this->once())
            ->method('getUrl')
            ->with('*/*/*', array('_current' => true))
            ->will($this->returnValue('http://example.com'));
        $this->assertEquals('result', $plugin->aroundDispatch($subjectMock, $closureMock, $requestMock));
    }
}

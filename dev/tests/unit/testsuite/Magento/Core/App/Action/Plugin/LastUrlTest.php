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
        $url = $this->getMock('\Magento\Url', array(), array(), '', false);
        $plugin = new \Magento\Core\App\Action\Plugin\LastUrl($session, $url);
        $session->expects($this->once())->method('setLastUrl')->with('http://example.com');
        $invocationChainMock = $this->getMock('Magento\Code\Plugin\InvocationChain', array(), array(), '', false);
        $invocationChainMock->expects($this->once())->method('proceed')->will($this->returnValue('result'));
        $url->expects($this->once())
            ->method('getUrl')
            ->with('*/*/*', array('_current' => true))
            ->will($this->returnValue('http://example.com'));
        $this->assertEquals('result', $plugin->aroundDispatch(array(), $invocationChainMock));
    }
}
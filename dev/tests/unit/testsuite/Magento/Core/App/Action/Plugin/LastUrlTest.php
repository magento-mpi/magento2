<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\App\Action\Plugin;

class LastUrlTests extends \PHPUnit_Framework_TestCase
{
    public function testAfterDispatch()
    {
        $session = $this->getMock('\Magento\Core\Model\Session', array('setLastUrl'), array(), '', false);
        $url = $this->getMock('\Magento\Core\Model\Url', array(), array(), '', false);
        $plugin = new \Magento\Core\App\Action\Plugin\LastUrl($session, $url);
        $session->expects($this->once())->method('setLastUrl')->with('http://example.com');
        $url->expects($this->once())
            ->method('getUrl')
            ->with('*/*/*', array('_current' => true))
            ->will($this->returnValue('http://example.com'));
        $this->assertEquals('result', $plugin->afterDispatch('result'));
    }
}
<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\App\Action\Plugin;

class DesignTest extends \PHPUnit_Framework_TestCase
{
    public function testAroundDispatch()
    {
        $designLoaderMock = $this->getMock('Magento\View\DesignLoader', array(), array(), '', false);
        $invocationChainMock = $this->getMock('Magento\Code\Plugin\InvocationChain', array(), array(), '', false);
        $plugin = new \Magento\Core\App\Action\Plugin\Design($designLoaderMock);
        $designLoaderMock->expects($this->once())->method('load');
        $invocationChainMock->expects($this->once())->method('proceed')->with(array());
        $plugin->aroundDispatch(array(), $invocationChainMock);
    }
}

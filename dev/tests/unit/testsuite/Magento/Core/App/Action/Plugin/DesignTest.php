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
        $subjectMock = $this->getMock('Magento\App\Action\Action', array(), array(), '', false);
        $designLoaderMock = $this->getMock('Magento\View\DesignLoader', array(), array(), '', false);
        $closureMock = function () {
            return 'Expected';
        };
        $requestMock = $this->getMock('Magento\App\RequestInterface');
        $plugin = new \Magento\Core\App\Action\Plugin\Design($designLoaderMock);
        $designLoaderMock->expects($this->once())->method('load');
        $this->assertEquals('Expected', $plugin->aroundDispatch($subjectMock, $closureMock, $requestMock));
    }
}

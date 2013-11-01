<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

class HttpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testProcessRequestBootstrapException()
    {
        if (!\Magento\TestFramework\Helper\Bootstrap::canTestHeaders()) {
            $this->markTestSkipped('Can\'t test application response without sending headers');
        }

        $objectManager = $this->getMock('Magento\ObjectManager');
        $objectManager->expects($this->any())
            ->method('get')
            ->will($this->throwException(new \Magento\BootstrapException('exception_message')));

        $areaListMock = $this->getMock('Magento\App\AreaList', array(), array(), '', false);
        $requestMock = $this->getMock('Magento\App\RequestInterface', array(), array(), '', false);
        $scopeMock = $this->getMock('Magento\Config\Scope', array(), array(), '', false);
        $configLoaderMock = $this->getMock('Magento\App\ObjectManager\ConfigLoader', array(), array(), '', false);

        /** @var \Magento\App\Http $model */
        $model = new \Magento\App\Http($objectManager, $areaListMock, $requestMock, $scopeMock, $configLoaderMock);
        ob_start();
        $model->execute();
        $content = ob_get_clean();

        $headers = xdebug_get_headers();
        $this->assertContains('Content-Type: text/plain', $headers);
        $this->assertEquals('exception_message', $content, 'The response must contain exception message, and only it');
    }
}

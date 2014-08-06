<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model;

class SessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var bool
     */
    public static $sessionStart = false;

    /**
     * @var bool
     */
    public static $registerShutdownFunction = false;

    /**
     * @covers Magento\Backend\Model\Session::__construct
     * @runInSeparateProcess
     */
    public function testConstructor()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        include __DIR__ . '/_files/session_backend_mock.php';
        $request = $helper->getObject('Magento\Framework\App\Request\Http');
        $configMock = $this->getMockBuilder('Magento\Framework\Session\Config\ConfigInterface')
            ->disableOriginalConstructor()->getMock();
        $configMock->expects($this->any())
            ->method('getOptions')
            ->willReturn([]);
        $helper->getObject('Magento\Backend\Model\Session', ['request' => $request, 'sessionConfig' => $configMock]);
        $this->assertTrue(self::$sessionStart);
        $this->assertTrue(self::$registerShutdownFunction);
    }
}

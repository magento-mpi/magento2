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
        require_once __DIR__ . '/../../_files/session_backend_mock.php';
        require_once __DIR__ . '/../../_files/session_set_name_mock.php';
        $requestMock = $helper->getObject('Magento\Framework\App\Request\Http');
        $helper->getObject('Magento\Backend\Model\Session', array('request' => $requestMock));
        $this->assertTrue(self::$sessionStart);
        $this->assertTrue(self::$registerShutdownFunction);
    }
}

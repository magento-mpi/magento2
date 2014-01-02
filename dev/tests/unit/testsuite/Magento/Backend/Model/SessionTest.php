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
        include(__DIR__. '/_files/session_backend_mock.php');
        $requestMock = $helper->getObject('Magento\App\Request\Http');
        $helper->getObject('Magento\Backend\Model\Session', array('request' => $requestMock));
        $this->assertTrue(self::$sessionStart);
        $this->assertTrue(self::$registerShutdownFunction);
    }
}

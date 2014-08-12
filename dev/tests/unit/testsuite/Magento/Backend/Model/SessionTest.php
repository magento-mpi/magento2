<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model;

/**
 * @bug https://github.com/sebastianbergmann/phpunit/issues/314
 * Workaround: use the "require_once" below and declare "preserveGlobalState disabled" in the test class
 */
require_once __DIR__ . '/../../../../framework/bootstrap.php';

/**
 * @runInSeparateProcess
 * @preserveGlobalState disabled
 */
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
     */
    public function testConstructor()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        require_once __DIR__ . '/../../_files/session_backend_mock.php';
        $requestMock = $helper->getObject('Magento\Framework\App\Request\Http');
        $helper->getObject('Magento\Backend\Model\Session', array('request' => $requestMock));
        $this->assertTrue(self::$sessionStart);
        $this->assertTrue(self::$registerShutdownFunction);
    }
}

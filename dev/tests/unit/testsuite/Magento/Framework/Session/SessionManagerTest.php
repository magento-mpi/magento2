<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
// @codingStandardsIgnoreStart
namespace {
    $mockPHPFunctions = false;
}

namespace Magento\Framework\Session {
    // @codingStandardsIgnoreEnd

    /**
     * Mock ini_set global function
     *
     * @param string $varName
     * @param string $newValue
     * @return bool|string
     */
    function ini_set($varName, $newValue)
    {
        global $mockPHPFunctions;
        if ($mockPHPFunctions) {
            SessionManagerTest::$isIniSetInvoked = true;
            SessionManagerTest::assertSame(SessionManagerTest::SESSION_USE_ONLY_COOKIES, $varName);
            SessionManagerTest::assertSame(SessionManagerTest::SESSION_USE_ONLY_COOKIES_ENABLE, $newValue);
            return true;
        }
        return call_user_func_array('\ini_set', func_get_args());
    }

    /**
     * Mock session_regenerate_id to fail if false is passed
     *
     * @param bool $var
     * @return bool
     */
    function session_regenerate_id($var)
    {
        global $mockPHPFunctions;
        if ($mockPHPFunctions) {
            SessionManagerTest::assertTrue($var);
            return true;
        }
        return call_user_func_array('\session_regenerate_id', func_get_args());
    }

    
    /**
     * Test SessionManager
     *
     */
    class SessionManagerTest extends \PHPUnit_Framework_TestCase
    {
        const SESSION_USE_ONLY_COOKIES = 'session.use_only_cookies';
        const SESSION_USE_ONLY_COOKIES_ENABLE = '1';

        /**
         * @var \Magento\TestFramework\Helper\ObjectManager
         */
        private $objectManager;

        /**
         * @var \Magento\Framework\Session\SessionManager
         */
        private $sessionManager;

        /**
         * @var \Magento\Framework\Session\Config\ConfigInterface | \PHPUnit_Framework_MockObject_MockObject
         */
        private $mockSessionConfig;

        /**
         * @var bool
         */
        public static $isIniSetInvoked;

        public function setUp()
        {
            global $mockPHPFunctions;
            $mockPHPFunctions = true;
            $this->mockSessionConfig = $this->getMockBuilder('\Magento\Framework\Session\Config\ConfigInterface')
                ->disableOriginalConstructor()
                ->getMock();

            $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
            $arguments = ['sessionConfig' => $this->mockSessionConfig];
            $this->sessionManager = $this->objectManager->getObject(
                'Magento\Framework\Session\SessionManager',
                $arguments
            );
        }

        public function testSessionManagerConstructor()
        {
            self::$isIniSetInvoked = false;
            $this->objectManager->getObject('Magento\Framework\Session\SessionManager');
            $this->assertTrue(SessionManagerTest::$isIniSetInvoked);
        }

        /**
         * @runInSeparateProcess
         */
        public function testRegenerateId()
        {
            require_once __DIR__ . '/../../_files/session_backend_mock.php';

            $this->mockSessionConfig->expects($this->once())
                ->method('getUseCookies')
                ->will($this->returnValue(false));
            $this->assertSame($this->sessionManager, $this->sessionManager->regenerateId());
        }
    }
}

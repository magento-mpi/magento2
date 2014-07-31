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
     * @return bool
     */
    function ini_set($varName, $newValue)
    {
        global $mockPHPFunctions;
        if ($mockPHPFunctions) {
            SessionManagerTest::assertSame(SessionManagerTest::SESSION_USE_ONLY_COOKIES, $varName);
            SessionManagerTest::assertSame(SessionManagerTest::SESSION_USE_ONLY_COOKIES_ENABLE, $newValue);
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
        }
        return call_user_func_array('\session_regenerate_id', func_get_args());
    }

    /**
     * Mock headers_sent to return false
     * 
     * @return bool
     */
    function headers_sent()
    {
        global $mockPHPFunctions;
        if ($mockPHPFunctions) {
            return false;
        }
        return call_user_func_array('\headers_sent', func_get_args());
    }

    use Magento\TestFramework\Helper\ObjectManager;

    /**
     * Test SessionManager
     *
     */
    class SessionManagerTest extends \PHPUnit_Framework_TestCase
    {
        const SESSION_USE_ONLY_COOKIES = 'session.use_only_cookies';
        const SESSION_USE_ONLY_COOKIES_ENABLE = '1';

        /**
         * @var ObjectManager
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

        public function setUp()
        {
            global $mockPHPFunctions;
            $mockPHPFunctions = true;
            $this->mockSessionConfig = $this->getMockBuilder('\Magento\Framework\Session\Config\ConfigInterface')
                ->disableOriginalConstructor()
                ->getMock();

            $this->objectManager = new ObjectManager($this);
            $arguments = ['sessionConfig' => $this->mockSessionConfig];
            $this->sessionManager = $this->objectManager->getObject(
                'Magento\Framework\Session\SessionManager',
                $arguments
            );
        }

        public function testSessionManagerConstructor()
        {
            $this->objectManager->getObject('Magento\Framework\Session\SessionManager');
            $expectedValue = '1';
            $sessionUseOnlyCookies = ini_get(self::SESSION_USE_ONLY_COOKIES);
            $this->assertSame($expectedValue, $sessionUseOnlyCookies);
        }

        public function testRegenerateId()
        {
            $this->mockSessionConfig->expects($this->once())
                ->method('getUseCookies')
                ->will($this->returnValue(false));
            $this->assertSame($this->sessionManager, $this->sessionManager->regenerateId());
        }
    }
}

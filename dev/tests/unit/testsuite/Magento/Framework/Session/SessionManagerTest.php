<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Session;

/**
 * Mock ini_set global function
 *
 * @param string $varName
 * @param string $newValue
 */
function ini_set($varName, $newValue)
{
    SessionManagerTest::assertSame(SessionManagerTest::SESSION_USE_ONLY_COOKIES, $varName);
    SessionManagerTest::assertSame(SessionManagerTest::SESSION_USE_ONLY_COOKIES_ENABLE, $newValue);

    call_user_func_array('\ini_set', func_get_args());
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

    public function testSessionUseOnlyCookies()
    {
        $objectManager = new ObjectManager($this);
        $objectManager->getObject(
            'Magento\Framework\Session\SessionManager'
        );
        $expectedValue = '1';
        $sessionUseOnlyCookies = ini_get(self::SESSION_USE_ONLY_COOKIES);
        $this->assertSame($expectedValue, $sessionUseOnlyCookies);
    }
}

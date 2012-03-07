<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_PageCache
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Enterprise_PageCache
 */
class Enterprise_PageCache_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_PageCache_Model_Observer
     */
    protected $_observer;

    protected function setUp()
    {
        $this->_observer = new Enterprise_PageCache_Model_Observer;
    }

    /**
     * Mark test skipped, if environment doesn't allow to send headers
     */
    protected function _requireSendCookieHeaders()
    {
        if (!Magento_Test_Bootstrap::canTestHeaders()) {
            $this->markTestSkipped('Test requires to send cookie headers.');
        }
    }

    public function testLaunchDesignEditor()
    {
        $this->_requireSendCookieHeaders();
        $noCacheCookieHeader = sprintf(
            'Set-Cookie: %s=1; path=/; httponly',
            Enterprise_PageCache_Model_Processor::NO_CACHE_COOKIE
        );
        $this->assertNotContains($noCacheCookieHeader, xdebug_get_headers());
        $this->_observer->launchDesignEditor(new Varien_Event_Observer());
        $this->assertContains($noCacheCookieHeader, xdebug_get_headers());
    }

    public function testExitDesignEditor()
    {
        $this->_requireSendCookieHeaders();
        $noCacheCookieHeader = sprintf(
            'Set-Cookie: %s=deleted; expires=Thu, 01-Jan-1970 00:00:01 GMT; path=/; httponly',
            Enterprise_PageCache_Model_Processor::NO_CACHE_COOKIE
        );
        $this->assertNotContains($noCacheCookieHeader, xdebug_get_headers());
        $this->_observer->exitDesignEditor(new Varien_Event_Observer());
        $this->assertContains($noCacheCookieHeader, xdebug_get_headers());
    }
}

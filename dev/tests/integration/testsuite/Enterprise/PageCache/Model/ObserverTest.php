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
    protected function _requireSendingCookieHeaders()
    {
        if (!Magento_Test_Bootstrap::canTestHeaders()) {
            $this->markTestSkipped('Test requires to send cookie headers.');
        }
    }

    /**
     * Retrieve array items that contain specific substring
     *
     * @param array $items
     * @param $substring
     * @return array
     */
    protected function _findItemsContainingText(array $items, $substring)
    {
        $result = array();
        foreach ($items as $oneItem) {
            if (strpos($oneItem, $substring) !== false) {
                $result[] = $oneItem;
            }
        }
        return $result;
    }

    public function testLaunchDesignEditor()
    {
        $this->_requireSendingCookieHeaders();
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
        $this->_requireSendingCookieHeaders();
        $noCacheCookieHeader = sprintf(
            'Set-Cookie: %s=deleted; expires=',
            Enterprise_PageCache_Model_Processor::NO_CACHE_COOKIE
        );
        $this->assertEmpty($this->_findItemsContainingText(xdebug_get_headers(), $noCacheCookieHeader));
        $this->_observer->exitDesignEditor(new Varien_Event_Observer());
        $this->assertNotEmpty($this->_findItemsContainingText(xdebug_get_headers(), $noCacheCookieHeader));
    }
}

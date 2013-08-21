<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Tags
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_CacheStorageManagement_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Checks if full page cache is enabled
     *
     * @return bool
     */
    public function isFullPageCacheEnabled()
    {
        $pageCacheXpath = "//td[normalize-space(text())='FPC']";

        $this->addParameter('indexName', 'FPC');
        if (!$this->controlIsPresent('checkbox', 'select_cache')) {
            $this->fail('Page Cache type is not present on the page');
        }
        $cacheEnabled = $this->_getControlXpath('pageelement', 'cache_enabled');
        $cacheInvalided = $this->_getControlXpath('pageelement', 'cache_invalided');
        if ($this->elementIsPresent($cacheEnabled . $pageCacheXpath)
            || $this->elementIsPresent($cacheInvalided . $pageCacheXpath)
        ) {
            return true;
        }
        return false;
    }

    /**
     * Enables Full page caching
     *
     * @return bool
     */
    public function enableFullPageCache()
    {
        if (!$this->isFullPageCacheEnabled()) {
            $this->clickControl('checkbox', 'select_cache', false);
            $this->fillDropdown('cache_action', 'Enable');
            $this->clickButton('submit');
        }
        if ($this->isFullPageCacheEnabled()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Disables Full page caching
     *
     * @return bool
     */
    public function disableFullPageCache()
    {
        if ($this->isFullPageCacheEnabled()) {
            $this->clickControl('checkbox', 'select_cache', false);
            $this->fillDropdown('cache_action', 'Disable');
            $this->clickButton('submit');
        }
        if (!$this->isFullPageCacheEnabled()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Refresh full page cache
     *
     * @return bool
     */
    public function refreshFullPageCache()
    {
        if ($this->isFullPageCacheEnabled()) {
            $this->clickControl('checkbox', 'select_cache', false);
            $this->fillDropdown('cache_action', 'Refresh');
            $this->clickButton('submit');

            $this->addParameter('qtySelected', 1);
            $this->assertMessagePresent('success', 'success_refreshed_cache');
        }
    }
}

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
class Enterprise2_Mage_CacheStorageManagement_Helper extends Mage_Selenium_TestCase
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
        if (!$this->isElementPresent($this->_findUimapElement('checkbox', 'select_cache'))) {
            $this->fail(
                'Page Cache type is not present on the page'
            );
        }
        if ($this->isElementPresent($this->_findUimapElement('pageelement', 'cache_enabled') . $pageCacheXpath) ||
            $this->isElementPresent($this->_findUimapElement('pageelement', 'cache_invalided') . $pageCacheXpath)) {
            return true;
        } else {
            return false;
        }
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
}
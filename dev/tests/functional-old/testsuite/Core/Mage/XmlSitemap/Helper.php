<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_XmlSitemap
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * XML SiteMap Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_XmlSitemap_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Generate URL for selected area
     *
     * @param string $url
     *
     * @return string
     */
    public function getFileUrl($url)
    {
        $urlRaw = $this->getConfigHelper()->getAreaBaseUrl('frontend');
        $currentAreaBaseUrl = preg_replace('/(\/index\.php\/?|\/)$/', '', $urlRaw);
        return $currentAreaBaseUrl . '/' . $url;
    }
}

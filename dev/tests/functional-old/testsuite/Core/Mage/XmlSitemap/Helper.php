<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * XML SiteMap Helper class
 *
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

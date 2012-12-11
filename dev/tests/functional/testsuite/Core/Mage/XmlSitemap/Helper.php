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
        $currentAreaBaseUrl = $this->getConfigHelper()->getAreaBaseUrl('frontend');
        return $currentAreaBaseUrl . $url;
    }

    /**
     * Get file from admin area
     * Suitable for reports testing
     *
     * @param string $url
     * @return string
     */
    public function getFile($url)
    {
        $cookie = $this->getCookie();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        $data = curl_exec($ch);
        $body=substr($data, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
        curl_close($ch);
        return $body;
    }
}

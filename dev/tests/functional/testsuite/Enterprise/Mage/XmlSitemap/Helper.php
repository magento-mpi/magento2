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
 * @method Community2_Mage_XmlSitemap_Helper helper(string $className)
 */
class Enterprise_Mage_XmlSitemap_Helper extends Mage_Selenium_AbstractHelper
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
        return $this->helper('Community2_Mage_XmlSitemap_Helper')->getFileUrl($url);
    }
}

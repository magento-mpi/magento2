<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Html notices block
 *
 * @category    Mage
 * @package     Mage_Page
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Page_Block_Html_Notices extends Mage_Core_Block_Template
{

    /**
     * Cookie restriction lifetime configuration path
     */
    const XML_PATH_COOKIE_RESTRICTION_LIFETIME = 'web/cookie/cookie_restriction_lifetime';


    /**
     * Check if noscript notice should be displayed
     *
     * @return boolean
     */
    public function displayNoscriptNotice()
    {
        return Mage::getStoreConfig('web/browser_capabilities/javascript');
    }

    /**
     * Check if demo store notice should be displayed
     *
     * @return boolean
     */
    public function displayDemoNotice()
    {
        return Mage::getStoreConfig('design/head/demonotice');
    }

    /**
     * Get cookie restriction lifetime (in seconds)
     *
     * @return int
     */
    public function getCookieRestrictionLifetime()
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_COOKIE_RESTRICTION_LIFETIME);
    }

    /**
     * Check if cookie restriction notice should be displayed
     *
     * @return bool
     */
    public function displayCookieRestrictionNotice()
    {
        $acceptedSaveCookiesWebsites = $this->_getAcceptedSaveCookiesWebsites();
        return Mage::getStoreConfig(self::XML_PATH_COOKIE_RESTRICTION) &&
            empty($acceptedSaveCookiesWebsites[Mage::app()->getWebsite()->getId()]);
    }

    /**
     * Get Link to cookie restriction privacy policy page
     *
     * @return string
     */
    public function getPrivacyPolicyLink()
    {
        return Mage::getUrl('privacy-policy-cookie-restriction-mode');
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Html notices block
 *
 * @category    Magento
 * @package     Magento_Page
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Page\Block\Html;

class Notices extends \Magento\Core\Block\Template
{
    /**
     * Check if noscript notice should be displayed
     *
     * @return boolean
     */
    public function displayNoscriptNotice()
    {
        return \Mage::getStoreConfig('web/browser_capabilities/javascript');
    }

    /**
     * Check if demo store notice should be displayed
     *
     * @return boolean
     */
    public function displayDemoNotice()
    {
        return \Mage::getStoreConfig('design/head/demonotice');
    }

    /**
     * Get Link to cookie restriction privacy policy page
     *
     * @return string
     */
    public function getPrivacyPolicyLink()
    {
        return \Mage::getUrl('privacy-policy-cookie-restriction-mode');
    }
}

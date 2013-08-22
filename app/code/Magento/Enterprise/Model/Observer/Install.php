<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Enterprise
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Installer observer
 *
 */
class Magento_Enterprise_Model_Observer_Install
{
    /**
     * Set Enterprise design theme and flag to hide iframe
     *
     * @param Magento_Event_Observer $observer
     */
    public function setDesignTheme($observer)
    {
        Mage::getSingleton('Magento_Install_Model_Installer')->setHideIframe(true);
    }
}

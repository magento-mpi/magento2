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
namespace Magento\Enterprise\Model\Observer;

class Install
{
    /**
     * Set Enterprise design theme and flag to hide iframe
     *
     * @param \Magento\Event\Observer $observer
     */
    public function setDesignTheme($observer)
    {
        \Mage::getSingleton('Magento\Install\Model\Installer')->setHideIframe(true);
    }
}

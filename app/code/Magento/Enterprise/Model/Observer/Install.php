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
     * @var Magento_Install_Model_Installer
     */
    protected $_installer;

    /**
     * @param Magento_Install_Model_Installer $installer
     */
    public function __construct(
        Magento_Install_Model_Installer $installer
    ) {
        $this->_installer = $installer;
    }

    /**
     * Set Enterprise design theme and flag to hide iframe
     *
     * @param Magento_Event_Observer $observer
     */
    public function setDesignTheme($observer)
    {
        $this->_installer->setHideIframe(true);
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Enterprise
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Enterprise\Model\Observer;

/**
 * Installer observer
 *
 */
class Install
{
    /**
     * @var \Magento\Install\Model\Installer
     */
    protected $_installer;

    /**
     * @param \Magento\Install\Model\Installer $installer
     */
    public function __construct(
        \Magento\Install\Model\Installer $installer
    ) {
        $this->_installer = $installer;
    }

    /**
     * Set Enterprise design theme and flag to hide iframe
     *
     * @param \Magento\Event\Observer $observer
     * @return void
     */
    public function setDesignTheme($observer)
    {
        $this->_installer->setHideIframe(true);
    }
}

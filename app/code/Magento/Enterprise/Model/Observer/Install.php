<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Enterprise\Model\Observer;

use Magento\Framework\Event\Observer as EventObserver;

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
    public function __construct(\Magento\Install\Model\Installer $installer)
    {
        $this->_installer = $installer;
    }

    /**
     * Set Enterprise design theme and flag to hide iframe
     *
     * @param EventObserver $observer
     * @return void
     */
    public function setDesignTheme($observer)
    {
        $this->_installer->setHideIframe(true);
    }
}

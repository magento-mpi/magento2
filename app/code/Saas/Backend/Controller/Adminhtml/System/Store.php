<?php
/**
 * Store controller
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Backend_Controller_Adminhtml_System_Store extends Magento_Adminhtml_Controller_System_Store
{
    /**
     * Backup database
     * Disable making backups
     *
     * @param string $failPath redirect path if backup failed
     * @param array $arguments
     * @return Magento_Adminhtml_Controller_System_Store
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _backupDatabase($failPath, $arguments = array())
    {
        return $this;
    }
}

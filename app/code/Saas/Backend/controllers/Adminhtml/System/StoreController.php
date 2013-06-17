<?php
/**
 * Store controller
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once 'Mage/Adminhtml/controllers/System/StoreController.php';

class Saas_Backend_Adminhtml_System_StoreController extends Mage_Adminhtml_System_StoreController
{
    /**
     * Backup database
     * Disable making backups
     *
     * @param string $failPath redirect path if backup failed
     * @param array $arguments
     * @return Mage_Adminhtml_System_StoreController
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _backupDatabase($failPath, $arguments = array())
    {
        return $this;
    }
}

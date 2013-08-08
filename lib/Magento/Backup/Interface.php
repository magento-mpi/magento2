<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backup
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface for work with archives
 *
 * @category    Magento
 * @package     Magento_Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Magento_Backup_Interface
{
    /**
     * Create Backup
     *
     * @return boolean
     */
    public function create();

    /**
     * Rollback Backup
     *
     * @return boolean
     */
    public function rollback();

    /**
     * Set Backup Extension
     *
     * @param string $backupExtension
     * @return Magento_Backup_Interface
     */
    public function setBackupExtension($backupExtension);

    /**
     * Set Resource Model
     *
     * @param object $resourceModel
     * @return Magento_Backup_Interface
     */
    public function setResourceModel($resourceModel);

    /**
     * Set Time
     *
     * @param int $time
     * @return Magento_Backup_Interface
     */
    public function setTime($time);

    /**
     * Get Backup Type
     *
     * @return string
     */
    public function getType();

    /**
     * Set path to directory where backups stored
     *
     * @param string $backupsDir
     * @return Magento_Backup_Interface
     */
    public function setBackupsDir($backupsDir);
}

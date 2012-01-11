<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backup
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface for work with archives
 *
 * @category    Mage
 * @package     Mage_Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Mage_Backup_Interface
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
     * @return Mage_Backup_Interface
     */
    public function setBackupExtension($backupExtension);

    /**
     * Set Resource Model
     *
     * @param object $resourceModel
     * @return Mage_Backup_Interface
     */
    public function setResourceModel($resourceModel);

    /**
     * Set Time
     *
     * @param int $time
     * @return Mage_Backup_Interface
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
     * @return Mage_Backup_Interface
     */
    public function setBackupsDir($backupsDir);
}

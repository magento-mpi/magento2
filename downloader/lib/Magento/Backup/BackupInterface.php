<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\Backup
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface for work with archives
 *
 * @category    Magento
 * @package     \Magento\Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backup;

interface BackupInterface
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
     * @return \Magento\Backup\BackupInterface
     */
    public function setBackupExtension($backupExtension);

    /**
     * Set Resource Model
     *
     * @param object $resourceModel
     * @return \Magento\Backup\BackupInterface
     */
    public function setResourceModel($resourceModel);

    /**
     * Set Time
     *
     * @param int $time
     * @return \Magento\Backup\BackupInterface
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
     * @return \Magento\Backup\BackupInterface
     */
    public function setBackupsDir($backupsDir);
}

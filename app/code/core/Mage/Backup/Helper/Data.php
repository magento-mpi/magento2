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
 * Backup data helper
 */
class Mage_Backup_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Backup type constant for database backup
     *
     * @const string
     */
    const TYPE_DB              = 'db';

    /**
     * Backup type constant for filesystem backup
     *
     * @const string
     */
    const TYPE_FILESYSTEM      = 'filesystem';

    /**
     * Backup type constant for full system backup(database + filesystem)
     *
     * @const string
     */
    const TYPE_SYSTEM_SNAPSHOT = 'snapshot';

    /**
     * Backup type constant for media and database backup
     *
     * @const string
     */
    const TYPE_MEDIA      = 'media';

    /**
     * Get all possible backup type values with descriptive title
     *
     * @return array
     */
    public function getBackupTypes()
    {
        return array(
            self::TYPE_DB => self::__('Database'),
            self::TYPE_MEDIA => self::__('Database and Media'),
            self::TYPE_SYSTEM_SNAPSHOT => self::__('System')
        );
    }

    /**
     * Get all possible backup type values
     *
     * @return array
     */
    public function getBackupTypesList()
    {
        return array(
            self::TYPE_DB,
            self::TYPE_SYSTEM_SNAPSHOT,
            self::TYPE_MEDIA
        );
    }

    /**
     * Get default backup type value
     *
     * @return string
     */
    public function getDefaultBackupType()
    {
        return self::TYPE_DB;
    }

    /**
     * Get directory path where backups stored
     *
     * @return string
     */
    public function getBackupsDir()
    {
        return Mage::getBaseDir('var') . DS . 'backups';
    }

    /**
     * Get backup file extension by backup type
     *
     * @param string $type
     * @return string
     */
    public function getExtensionByType($type)
    {
        $extensions = $this->getExtensions();
        return isset($extensions[$type]) ? $extensions[$type] : '';
    }

    /**
     * Get all types to extensions map
     *
     * @return array
     */
    public function getExtensions()
    {
        return array(
            self::TYPE_SYSTEM_SNAPSHOT => 'tgz',
            self::TYPE_MEDIA => 'tgz',
            self::TYPE_DB => 'gz'
        );
    }

    /**
     * Generate backup download name
     *
     * @param Mage_Backup_Model_Backup $backup
     * @return string
     */
    public function generateBackupDownloadName(Mage_Backup_Model_Backup $backup)
    {
        $additionalExtension = $backup->getType() == self::TYPE_DB ? '.sql' : '';
        return $backup->getType() . '-' . date('YmdHis', $backup->getTime()) . $additionalExtension . '.'
            . $this->getExtensionByType($backup->getType());
    }

    /**
     * Check Permission for Rollback
     *
     * @return boolean
     */
    public function isRollbackAllowed(){
        return Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed('system/tools/backup/rollback' );
    }

    /**
     * Get paths that should be ignored when creating system snapshots
     *
     * @return array
     */
    public function getBackupIgnorePaths()
    {
        return array(
            '.svn',
            'maintenance.flag',
            Mage::getBaseDir('var') . DS . 'session',
            Mage::getBaseDir('var') . DS . 'cache',
            Mage::getBaseDir('var') . DS . 'full_page_cache',
            Mage::getBaseDir('var') . DS . 'locks',
            Mage::getBaseDir('var') . DS . 'log',
            Mage::getBaseDir('var') . DS . 'report'
        );
    }

    /**
     * Get paths that should be ignored when rolling back system snapshots
     *
     * @return array
     */
    public function getRollbackIgnorePaths()
    {
        return array(
            '.svn',
            'maintenance.flag',
            Mage::getBaseDir('var') . DS . 'session',
            Mage::getBaseDir('var') . DS . 'locks',
            Mage::getBaseDir('var') . DS . 'log',
            Mage::getBaseDir('var') . DS . 'report',
            Mage::getBaseDir('app') . DS . 'Mage.php',
            Mage::getBaseDir() . DS . 'errors',
            Mage::getBaseDir() . DS . 'index.php'
        );
    }

    /**
     * Put store into maintenance mode
     *
     * @return bool
     */
    public function turnOnMaintenanceMode()
    {
        $maintenanceFlagFile = $this->getMaintenanceFlagFilePath();
        $result = file_put_contents($maintenanceFlagFile, 'maintenance');

        return $result !== false;
    }

    /**
     * Turn off store maintenance mode
     */
    public function turnOffMaintenanceMode()
    {
        $maintenanceFlagFile = $this->getMaintenanceFlagFilePath();
        @unlink($maintenanceFlagFile);
    }

    /**
     * Get backup create success message by backup type
     *
     * @param string $type
     * @return string
     */
    public function getCreateSuccessMessageByType($type)
    {
        $messagesMap = array(
            self::TYPE_SYSTEM_SNAPSHOT => $this->__('The system backup has been created.'),
            self::TYPE_MEDIA => $this->__('The database and media backup has been created.'),
            self::TYPE_DB => $this->__('The database backup has been created.')
        );

        if (!isset($messagesMap[$type])) {
            return;
        }

        return $messagesMap[$type];
    }

    /**
     * Get path to maintenance flag file
     *
     * @return string
     */
    protected function getMaintenanceFlagFilePath()
    {
        return Mage::getBaseDir() . DS . 'maintenance.flag';
    }

    /**
     * Invalidate Cache
     * @return Mage_Backup_Helper_Data
     */
    public function invalidateCache()
    {
        if ($cacheTypesNode = Mage::getConfig()->getNode(Mage_Core_Model_Cache::XML_PATH_TYPES)) {
            $cacheTypesList = array_keys($cacheTypesNode->asArray());
            Mage::app()->getCacheInstance()->invalidateType($cacheTypesList);
        }
        return $this;
    }

    /**
     * Invalidate Indexer
     *
     * @return Mage_Backup_Helper_Data
     */
    public function invalidateIndexer()
    {
        foreach (Mage::getResourceModel('Mage_Index_Model_Resource_Process_Collection') as $process){
            $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        }
        return $this;
    }
}

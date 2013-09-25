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
 * Class to work with full filesystem and database backups
 *
 * @category    Magento
 * @package     Magento_Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backup_Snapshot extends Magento_Backup_Filesystem
{
    /**
     * Database backup manager
     *
     * @var Magento_Backup_Db
     */
    protected $_dbBackupManager;

    /**
     * Dirs instance
     *
     * @var Magento_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * @param Magento_Core_Model_Dir $dirs
     */
    public function __construct(
        Magento_Core_Model_Dir $dirs
    ) {
        $this->_dirs = $dirs;
    }

    /**
     * Implementation Rollback functionality for Snapshot
     *
     * @throws Exception
     * @return bool
     */
    public function rollback()
    {
        $result = parent::rollback();

        $this->_lastOperationSucceed = false;

        try {
            $this->_getDbBackupManager()->rollback();
        } catch (Exception $e) {
            $this->_removeDbBackup();
            throw $e;
        }

        $this->_removeDbBackup();
        $this->_lastOperationSucceed = true;

        return $result;
    }

    /**
     * Implementation Create Backup functionality for Snapshot
     *
     * @throws Exception
     * @return bool
     */
    public function create()
    {
        $this->_getDbBackupManager()->create();

        try {
            $result = parent::create();
        } catch (Exception $e) {
            $this->_removeDbBackup();
            throw $e;
        }

        $this->_lastOperationSucceed = false;
        $this->_removeDbBackup();
        $this->_lastOperationSucceed = true;

        return $result;
    }

    /**
     * Overlap getType
     *
     * @return string
     * @see Magento_Backup_Interface::getType()
     */
    public function getType()
    {
        return 'snapshot';
    }

    /**
     * Create Db Instance
     *
     * @return Magento_Backup_Interface
     */
    protected function _createDbBackupInstance()
    {
        return Magento_Backup::getBackupInstance(Magento_Backup_Helper_Data::TYPE_DB)
            ->setBackupExtension('gz')
            ->setTime($this->getTime())
            ->setBackupsDir($this->_dirs->getDir('var'))
            ->setResourceModel($this->getResourceModel());
    }

    /**
     * Get database backup manager
     *
     * @return Magento_Backup_Db
     */
    protected function _getDbBackupManager()
    {
        if (is_null($this->_dbBackupManager)) {
            $this->_dbBackupManager = $this->_createDbBackupInstance();
        }

        return $this->_dbBackupManager;
    }

    /**
     * Set Db backup manager
     *
     * @param Magento_Backup_Abstract $manager
     * @return Magento_Backup_Snapshot
     */
    public function setDbBackupManager(Magento_Backup_Abstract $manager)
    {
        $this->_dbBackupManager = $manager;
        return $this;
    }

    /**
     * Get Db Backup Filemane
     *
     * @return string
     */
    public function getDbBackupFilename()
    {
        return $this->_getDbBackupManager()->getBackupFilename();
    }

    /**
     * Remove Db backup after added it to the snapshot
     *
     * @return Magento_Backup_Snapshot
     */
    protected function _removeDbBackup()
    {
        @unlink($this->_getDbBackupManager()->getBackupPath());
        return $this;
    }
}

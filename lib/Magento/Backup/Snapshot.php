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
 * Class to work with full filesystem and database backups
 *
 * @category    Magento
 * @package     \Magento\Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backup;

class Snapshot extends \Magento\Backup\Filesystem
{
    /**
     * Database backup manager
     *
     * @var \Magento\Backup\Db
     */
    protected $_dbBackupManager;

    /**
     * Dirs instance
     *
     * @var \Magento\Core\Model\Dir
     */
    protected $_dirs;

    /**
     * @param \Magento\Core\Model\Dir $dirs
     */
    public function __construct(
        \Magento\Core\Model\Dir $dirs
    ) {
        $this->_dirs = $dirs;
    }

    /**
     * Implementation Rollback functionality for Snapshot
     *
     * @throws \Exception
     * @return bool
     */
    public function rollback()
    {
        $result = parent::rollback();

        $this->_lastOperationSucceed = false;

        try {
            $this->_getDbBackupManager()->rollback();
        } catch (\Exception $e) {
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
     * @throws \Exception
     * @return bool
     */
    public function create()
    {
        $this->_getDbBackupManager()->create();

        try {
            $result = parent::create();
        } catch (\Exception $e) {
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
     * @see \Magento\Backup\BackupInterface::getType()
     */
    public function getType()
    {
        return 'snapshot';
    }

    /**
     * Create Db Instance
     *
     * @return \Magento\Backup\BackupInterface
     */
    protected function _createDbBackupInstance()
    {
        return \Magento\Backup::getBackupInstance(\Magento\Backup\Helper\Data::TYPE_DB)
            ->setBackupExtension('gz')
            ->setTime($this->getTime())
            ->setBackupsDir($this->_dirs->getDir('var'))
            ->setResourceModel($this->getResourceModel());
    }

    /**
     * Get database backup manager
     *
     * @return \Magento\Backup\Db
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
     * @param \Magento\Backup\AbstractBackup $manager
     * @return \Magento\Backup\Snapshot
     */
    public function setDbBackupManager(\Magento\Backup\AbstractBackup $manager)
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
     * @return \Magento\Backup\Snapshot
     */
    protected function _removeDbBackup()
    {
        @unlink($this->_getDbBackupManager()->getBackupPath());
        return $this;
    }
}

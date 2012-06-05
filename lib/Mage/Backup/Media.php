<?php
/**
 * {license_notice}
 *
 * @category     Mage
 * @package      Mage_Backup
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class to work media folder and database backups
 *
 * @category    Mage
 * @package     Mage_Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backup_Media extends Mage_Backup_Abstract
{
    /**
     * Snapshot backup manager innstance
     *
     * @var Mage_Backup_Snapshot
     */
    protected $_snapshot;

    /**
     * Set snapshot backup manager
     * @param Mage_Backup_Snapshot $snapshot
     */
    public function setSnapshotManager($snapshot)
    {
        $this->_snapshot = $snapshot;
    }

    /**
     * Get snapshot backup manager
     * @return Mage_Backup_Snapshot
     */
    public function getSnapshotManager()
    {
        if (null === $this->_snapshot) {
            $this->_snapshot = new Mage_Backup_Snapshot();
        }
        return $this->_snapshot;
    }

    /**
     * Implementation Rollback functionality for Snapshot
     *
     * @throws Mage_Exception
     * @return bool
     */
    public function rollback()
    {
        $this->_prepareIgnoreList();
        return $this->getSnapshotManager()->rollback();
    }

    /**
     * Implementation Create Backup functionality for Snapshot
     *
     * @throws Mage_Exception
     * @return bool
     */
    public function create()
    {
        $this->_prepareIgnoreList();
        return $this->getSnapshotManager()->create();
    }

    /**
     * Overlap getType
     *
     * @return string
     * @see Mage_Backup_Interface::getType()
     */
    public function getType()
    {
        return 'media';
    }

    /**
     * Add all folders and files except media and db backup to ignore list
     *
     * @return Mage_Backup_Media
     */
    protected function _prepareIgnoreList()
    {
        $map = array(
            $this->getSnapshotManager()->getRootDir() => array('media', 'var', 'pub'),
            $this->getSnapshotManager()->getRootDir() . DS . 'pub' => array('media'),
            $this->getSnapshotManager()->getRootDir() . DS . 'var' => array($this->getSnapshotManager()->getDbBackupFilename()),
        );

        foreach($map as $path => $whiteList) {
            foreach (new DirectoryIterator($path) as $item) {
                $filename = $item->getFilename();
                if (!$item->isDot() && !in_array($filename, $whiteList)) {
                    $this->getSnapshotManager()->addIgnorePaths($item->getPathname());
                }
            }
        }

        return $this;
    }

    /**
     * Set Backup Extension
     *
     * @param string $backupExtension
     * @return Mage_Backup_Interface
     */
    public function setBackupExtension($backupExtension)
    {
        $this->getSnapshotManager()->setBackupExtension($backupExtension);
        return $this;
    }

    /**
     * Set Resource Model
     *
     * @param object $resourceModel
     * @return Mage_Backup_Interface
     */
    public function setResourceModel($resourceModel)
    {
        $this->getSnapshotManager()->setResourceModel($resourceModel);
        return $this;
    }

    /**
     * Set Time
     *
     * @param int $time
     * @return Mage_Backup_Interface
     */
    public function setTime($time)
    {
        $this->getSnapshotManager()->setTime($time);
        return $this;
    }

    /**
     * Set path to directory where backups stored
     *
     * @param string $backupsDir
     * @return Mage_Backup_Interface
     */
    public function setBackupsDir($backupsDir)
    {
        $this->getSnapshotManager()->setBackupsDir($backupsDir);
        return $this;
    }

    /**
     * Add path that should be ignoring when creating or rolling back backup
     *
     * @param string|array $paths
     * @return Mage_Backup_Interface
     */
    public function addIgnorePaths($paths)
    {
        $this->getSnapshotManager()->addIgnorePaths($paths);
        return $this;
    }

    /**
     * Set root directory of Magento installation
     *
     * @param string $rootDir
     * @throws Mage_Exception
     * @return Mage_Backup_Interface
     */
    public function setRootDir($rootDir)
    {
        $this->getSnapshotManager()->setRootDir($rootDir);
        return $this;
    }
}

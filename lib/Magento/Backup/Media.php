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
 * Class to work media folder and database backups
 *
 * @category    Magento
 * @package     \Magento\Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backup;

class Media extends \Magento\Backup\AbstractBackup
{
    /**
     * Snapshot backup manager instance
     *
     * @var \Magento\Backup\Snapshot
     */
    protected $_snapshotManager;

    /**
     * Initialize backup manager instance
     *
     * @param \Magento\Backup\Snapshot|null $snapshotManager
     */
    public function __construct($snapshotManager = null)
    {
        if ($snapshotManager !== null) {
            if (!$snapshotManager instanceof \Magento\Backup\Snapshot) {
                throw new \Magento\MagentoException('Snapshot manager must be instance of \Magento\Backup\Snapshot');
            }
            $this->_snapshotManager = $snapshotManager;
        } else {
            $this->_snapshotManager = new \Magento\Backup\Snapshot();
        }
    }

    /**
     * Implementation Rollback functionality for Snapshot
     *
     * @throws \Magento\MagentoException
     * @return bool
     */
    public function rollback()
    {
        $this->_prepareIgnoreList();
        return $this->_snapshotManager->rollback();
    }

    /**
     * Implementation Create Backup functionality for Snapshot
     *
     * @throws \Magento\MagentoException
     * @return bool
     */
    public function create()
    {
        $this->_prepareIgnoreList();
        return $this->_snapshotManager->create();
    }

    /**
     * Overlap getType
     *
     * @return string
     * @see \Magento\Backup\BackupInterface::getType()
     */
    public function getType()
    {
        return 'media';
    }

    /**
     * Add all folders and files except media and db backup to ignore list
     *
     * @return \Magento\Backup\Media
     */
    protected function _prepareIgnoreList()
    {
        $rootDir = $this->_snapshotManager->getRootDir();
        $map = array(
            $rootDir => array('media', 'var', 'pub'),
            $rootDir . DIRECTORY_SEPARATOR . 'pub' => array('media'),
            $rootDir . DIRECTORY_SEPARATOR . 'var' => array($this->_snapshotManager->getDbBackupFilename()),
        );

        foreach ($map as $path => $whiteList) {
            foreach (new \DirectoryIterator($path) as $item) {
                $filename = $item->getFilename();
                if (!$item->isDot() && !in_array($filename, $whiteList)) {
                    $this->_snapshotManager->addIgnorePaths($item->getPathname());
                }
            }
        }

        return $this;
    }

    /**
     * Set Backup Extension
     *
     * @param string $backupExtension
     * @return \Magento\Backup\BackupInterface
     */
    public function setBackupExtension($backupExtension)
    {
        $this->_snapshotManager->setBackupExtension($backupExtension);
        return $this;
    }

    /**
     * Set Resource Model
     *
     * @param object $resourceModel
     * @return \Magento\Backup\BackupInterface
     */
    public function setResourceModel($resourceModel)
    {
        $this->_snapshotManager->setResourceModel($resourceModel);
        return $this;
    }

    /**
     * Set Time
     *
     * @param int $time
     * @return \Magento\Backup\BackupInterface
     */
    public function setTime($time)
    {
        $this->_snapshotManager->setTime($time);
        return $this;
    }

    /**
     * Set path to directory where backups stored
     *
     * @param string $backupsDir
     * @return \Magento\Backup\BackupInterface
     */
    public function setBackupsDir($backupsDir)
    {
        $this->_snapshotManager->setBackupsDir($backupsDir);
        return $this;
    }

    /**
     * Add path that should be ignoring when creating or rolling back backup
     *
     * @param string|array $paths
     * @return \Magento\Backup\BackupInterface
     */
    public function addIgnorePaths($paths)
    {
        $this->_snapshotManager->addIgnorePaths($paths);
        return $this;
    }

    /**
     * Set root directory of Magento installation
     *
     * @param string $rootDir
     * @throws \Magento\MagentoException
     * @return \Magento\Backup\BackupInterface
     */
    public function setRootDir($rootDir)
    {
        $this->_snapshotManager->setRootDir($rootDir);
        return $this;
    }
}

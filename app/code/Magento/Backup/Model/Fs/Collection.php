<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backup data collection
 */
namespace Magento\Backup\Model\Fs;

class Collection extends \Magento\Data\Collection\Filesystem
{
    /**
     * Folder, where all backups are stored
     *
     * @var string
     */
    protected $_baseDir;

    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * Backup data
     *
     * @var \Magento\Backup\Helper\Data
     */
    protected $_backupData = null;

    /**
     * Directory model
     *
     * @var \Magento\Core\Model\Dir
     */
    protected $_dir;

    /**
     * Set collection specific parameters and make sure backups folder will exist
     *
     * @param \Magento\Backup\Helper\Data $backupData
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Core\Model\Dir $dir
     */
    public function __construct(
        \Magento\Backup\Helper\Data $backupData,
        \Magento\Filesystem $filesystem,
        \Magento\Core\Model\Dir $dir
    ) {
        parent::__construct();

        $this->_backupData = $backupData;
        $this->_filesystem = $filesystem;
        $this->_dir = $dir;
        $this->_baseDir = $this->_dir->getDir(\Magento\Core\Model\Dir::VAR_DIR) . DS . 'backups';

        $this->_filesystem->setIsAllowCreateDirectories(true);
        $this->_filesystem->ensureDirectoryExists($this->_baseDir);
        $this->_filesystem->setWorkingDirectory($this->_baseDir);
        $this->_hideBackupsForApache();

        // set collection specific params
        $extensions = $this->_backupData->getExtensions();

        foreach ($extensions as $value) {
            $extensions[] = '(' . preg_quote($value, '/') . ')';
        }
        $extensions = implode('|', $extensions);

        $this->setOrder('time', self::SORT_ORDER_DESC)
            ->addTargetDir($this->_baseDir)
            ->setFilesFilter('/^[a-z0-9\-\_]+\.' . $extensions . '$/')
            ->setCollectRecursively(false);
    }

    /**
     * Create .htaccess file and deny backups directory access from web
     */
    protected function _hideBackupsForApache()
    {
        $htaccessPath = $this->_baseDir . DS . '.htaccess';
        if (!$this->_filesystem->isFile($htaccessPath)) {
            $this->_filesystem->write($htaccessPath, 'deny from all');
            $this->_filesystem->changePermissions($htaccessPath, 0644);
        }
    }

    /**
     * Get backup-specific data from model for each row
     *
     * @param string $filename
     * @return array
     */
    protected function _generateRow($filename)
    {
        $row = parent::_generateRow($filename);
        foreach (\Mage::getSingleton('Magento\Backup\Model\Backup')->load($row['basename'], $this->_baseDir)
            ->getData() as $key => $value) {
            $row[$key] = $value;
        }
        $row['size'] = $this->_filesystem->getFileSize($filename);
        $row['id'] = $row['time'] . '_' . $row['type'];
        return $row;
    }
}

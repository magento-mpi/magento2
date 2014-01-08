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
     * @var \Magento\Filesystem\Directory\WriteInterface
     */
    protected $_varDirectory;

    /**
     * Folder, where all backups are stored
     *
     * @var string
     */
    protected $_path = 'backups';

    /**
     * Backup data
     *
     * @var \Magento\Backup\Helper\Data
     */
    protected $_backupData = null;

    /**
     * Backup model
     *
     * @var \Magento\Backup\Model\Backup
     */
    protected $_backup = null;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Backup\Helper\Data $backupData
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Backup\Model\Backup $backup
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Backup\Helper\Data $backupData,
        \Magento\Filesystem $filesystem,
        \Magento\Backup\Model\Backup $backup
    ) {
        $this->_backupData = $backupData;
        parent::__construct($entityFactory);

        $this->_filesystem = $filesystem;
        $this->_backup = $backup;
        $this->_varDirectory = $filesystem->getDirectoryWrite(\Magento\Filesystem::VAR_DIR);

        $this->_hideBackupsForApache();

        // set collection specific params
        $extensions = $this->_backupData->getExtensions();

        foreach ($extensions as $value) {
            $extensions[] = '(' . preg_quote($value, '/') . ')';
        }
        $extensions = implode('|', $extensions);

        $this->_varDirectory->create($this->_path);
        $path = rtrim($this->_varDirectory->getAbsolutePath($this->_path), '/') . '/';
        $this->setOrder('time', self::SORT_ORDER_DESC)
            ->addTargetDir($path)
            ->setFilesFilter('/^[a-z0-9\-\_]+\.' . $extensions . '$/')
            ->setCollectRecursively(false);
    }

    /**
     * Create .htaccess file and deny backups directory access from web
     */
    protected function _hideBackupsForApache()
    {
        $filename = '.htaccess';
        if (!$this->_varDirectory->isFile($filename)) {
            $this->_varDirectory->writeFile($filename, 'deny from all');
            $this->_varDirectory->changePermissions($filename, 0644);
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
        foreach ($this->_backup->load($row['basename'], $this->_varDirectory->getAbsolutePath($this->_path))
            ->getData() as $key => $value) {
            $row[$key] = $value;
        }
        $row['size'] = $this->_varDirectory->stat($this->_varDirectory->getRelativePath($filename))['size'];
        $row['id'] = $row['time'] . '_' . $row['type'];
        return $row;
    }
}

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
 * Backup data collection
 *
 * @category   Magento
 * @package    Magento_Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backup_Model_Fs_Collection extends \Magento\Data\Collection\Filesystem
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
     * Set collection specific parameters and make sure backups folder will exist
     *
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(\Magento\Filesystem $filesystem)
    {
        parent::__construct();

        $this->_baseDir = Mage::getBaseDir('var') . DS . 'backups';
        $this->_filesystem = $filesystem;
        $this->_filesystem->setIsAllowCreateDirectories(true);
        $this->_filesystem->ensureDirectoryExists($this->_baseDir);
        $this->_filesystem->setWorkingDirectory($this->_baseDir);

        $this->_hideBackupsForApache();

        // set collection specific params
        $extensions = Mage::helper('Magento_Backup_Helper_Data')->getExtensions();

        foreach ($extensions as $value) {
            $extensions[] = '(' . preg_quote($value, '/') . ')';
        }
        $extensions = implode('|', $extensions);

        $this
            ->setOrder('time', self::SORT_ORDER_DESC)
            ->addTargetDir($this->_baseDir)
            ->setFilesFilter('/^[a-z0-9\-\_]+\.' . $extensions . '$/')
            ->setCollectRecursively(false)
        ;
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
        foreach (Mage::getSingleton('Magento_Backup_Model_Backup')->load($row['basename'], $this->_baseDir)
            ->getData() as $key => $value) {
            $row[$key] = $value;
        }
        $row['size'] = $this->_filesystem->getFileSize($filename);
        $row['id'] = $row['time'] . '_' . $row['type'];
        return $row;
    }
}

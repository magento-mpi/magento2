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
 * Backup data collection
 *
 * @category   Mage
 * @package    Mage_Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backup_Model_Fs_Collection extends Varien_Data_Collection_Filesystem
{
    /**
     * Folder, where all backups are stored
     *
     * @var string
     */
    protected $_baseDir;

    /**
     * Set collection specific parameters and make sure backups folder will exist
     */
    public function __construct()
    {
        parent::__construct();

        $this->_baseDir = Mage::getBaseDir('var') . DS . 'backups';

        // check for valid base dir
        $ioProxy = new Varien_Io_File();
        $ioProxy->mkdir($this->_baseDir);
        if (!is_file($this->_baseDir . DS . '.htaccess')) {
            $ioProxy->open(array('path' => $this->_baseDir));
            $ioProxy->write('.htaccess', 'deny from all', 0644);
        }

        // set collection specific params
        $extensions = Mage::helper('Mage_Backup_Helper_Data')->getExtensions();

        foreach ($extensions as $key => $value) {
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
     * Get backup-specific data from model for each row
     *
     * @param string $filename
     * @return array
     */
    protected function _generateRow($filename)
    {
        $row = parent::_generateRow($filename);
        foreach (Mage::getSingleton('Mage_Backup_Model_Backup')->load($row['basename'], $this->_baseDir)
            ->getData() as $key => $value) {
            $row[$key] = $value;
        }
        $row['size'] = filesize($filename);
        $row['id'] = $row['time'] . '_' . $row['type'];
        return $row;
    }
}

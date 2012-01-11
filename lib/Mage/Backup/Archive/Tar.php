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
 * Extended version of Mage_Archive_Tar that supports filtering
 *
 * @category    Mage
 * @package     Mage_Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backup_Archive_Tar extends Mage_Archive_Tar
{
    /**
     * Filenames or filename parts that are used for filtering files
     *
     * @var array()
     */
    protected $_skipFiles = array();

    /**
     * Overridden Mage_Archive_Tar::_createTar method that does the same actions as it's parent but filters
     * files using Mage_Backup_Filesystem_Iterator_Filter
     *
     * @see Mage_Archive_Tar::_createTar()
     * @param bool $skipRoot
     * @param bool $finalize
     */
    protected function _createTar($skipRoot = false, $finalize = false)
    {
        $path = $this->_getCurrentFile();

        $filesystemIterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST
        );

        $iterator = new Mage_Backup_Filesystem_Iterator_Filter($filesystemIterator, $this->_skipFiles);

        foreach ($iterator as $item) {
            $this->_setCurrentFile($item->__toString());
            $this->_packAndWriteCurrentFile();
        }

        if ($finalize) {
            $this->_getWriter()->write(str_repeat("\0", self::TAR_BLOCK_SIZE * 12));
        }
    }

    /**
     * Set files that shouldn't be added to tarball
     *
     * @param array $skipFiles
     * @return Mage_Backup_Archive_Tar
     */
    public function setSkipFiles(array $skipFiles)
    {
        $this->_skipFiles = $skipFiles;
        return $this;
    }
}

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
 * Class to work media folder and database backups
 *
 * @category    Mage
 * @package     Mage_Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backup_Media extends Mage_Backup_Snapshot
{
    /**
     * Implementation Rollback functionality for Snapshot
     *
     * @throws Mage_Exception
     * @return bool
     */
    public function rollback()
    {
        $this->_prepareIgnoreList();
        return parent::rollback();
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
        return parent::create();
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
        $iterator = new DirectoryIterator($this->getRootDir());

        foreach ($iterator as $item) {
            $filename = $item->getFilename();
            if (!in_array($filename, array('media', 'var'))) {
                $this->addIgnorePaths($item->getPathname());
            }
        }

        $iterator = new DirectoryIterator($this->getRootDir() . DS . 'var');
        $dbBackupFilename = $this->_getDbBackupManager()->getBackupFilename();

        foreach ($iterator as $item) {
            $filename = $item->getFilename();
            if ($filename != $dbBackupFilename) {
                $this->addIgnorePaths($item->getPathname());
            }
        }

        return $this;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class to work media folder and database backups
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Framework\Backup;

class Media extends \Magento\Framework\Backup\Snapshot
{
    /**
     * Implementation Rollback functionality for Media
     *
     * @throws \Magento\Framework\Exception
     * @return bool
     */
    public function rollback()
    {
        $this->_prepareIgnoreList();
        return parent::rollback();
    }

    /**
     * Implementation Create Backup functionality for Media
     *
     * @throws \Magento\Framework\Exception
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
     * @see \Magento\Framework\Backup\BackupInterface::getType()
     */
    public function getType()
    {
        return 'media';
    }

    /**
     * Add all folders and files except media and db backup to ignore list
     *
     * @return \Magento\Framework\Backup\Media
     */
    protected function _prepareIgnoreList()
    {
        $rootDir = $this->getRootDir();
        $map = array(
            $rootDir => array('media', 'var', 'pub'),
            $rootDir . '/pub' => array('media'),
            $rootDir . '/var' => array($this->getDbBackupFilename())
        );

        foreach ($map as $path => $whiteList) {
            foreach (new \DirectoryIterator($path) as $item) {
                $filename = $item->getFilename();
                if (!$item->isDot() && !in_array($filename, $whiteList)) {
                    $this->addIgnorePaths($item->getPathname());
                }
            }
        }

        return $this;
    }
}

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
 * Class to work system backup that excludes media folder
 *
 * @category    Magento
 * @package     \Magento\Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class \Magento\Backup\Nomedia extends \Magento\Backup\Media
{
    /**
     * Overlap getType
     *
     * @return string
     * @see \Magento\Backup\BackupInterface::getType()
     */
    public function getType()
    {
        return 'nomedia';
    }

    /**
     * Add media folder to ignore list
     *
     * @return \Magento\Backup\Media
     */
    protected function _prepareIgnoreList()
    {
        $rootDir = $this->_snapshotManager->getRootDir();
        $this->_snapshotManager->addIgnorePaths(array(
            $rootDir . DIRECTORY_SEPARATOR . 'media',
            $rootDir . DIRECTORY_SEPARATOR . 'pub' . DIRECTORY_SEPARATOR . 'media',
        ));
        return $this;
    }
}
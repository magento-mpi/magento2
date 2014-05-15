<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class to work system backup that excludes media folder
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Framework\Backup;

class Nomedia extends \Magento\Framework\Backup\Media
{
    /**
     * Overlap getType
     *
     * @return string
     * @see \Magento\Framework\Backup\BackupInterface::getType()
     */
    public function getType()
    {
        return 'nomedia';
    }

    /**
     * Add media folder to ignore list
     *
     * @return \Magento\Framework\Backup\Media
     */
    protected function _prepareIgnoreList()
    {
        $rootDir = $this->getRootDir();
        $this->addIgnorePaths(array($rootDir . '/media', $rootDir . '/pub/media'));
        return $this;
    }
}

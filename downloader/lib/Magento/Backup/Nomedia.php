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
 * Class to work system backup that excludes media folder
 *
 * @category    Magento
 * @package     Magento_Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backup;

class Nomedia extends \Magento\Backup\Media
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
        $rootDir = $this->getRootDir();
        $this->addIgnorePaths(array(
            $rootDir . '/media',
            $rootDir . '/pub/media',
        ));
        return $this;
    }
}

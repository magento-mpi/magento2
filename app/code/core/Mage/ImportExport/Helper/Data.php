<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * ImportExport data helper
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * XML path for config data
     */
    const XML_PATH_EXPORT_LOCAL_VALID_PATH = 'general/file/importexport_local_valid_paths';
    const XML_PATH_BUNCH_SIZE = 'general/file/bunch_size';

    /**
     * Maximum size of uploaded files.
     *
     * @return int
     */
    public function getMaxUploadSize()
    {
        return min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
    }

    /**
     * Get valid path masks to files for importing/exporting
     *
     * @return array
     */
    public function getLocalValidPaths()
    {
        $paths = Mage::getStoreConfig(self::XML_PATH_EXPORT_LOCAL_VALID_PATH);
        return $paths;
    }

    /**
     * Retrieve size of bunch (how much products should be involved in one import iteration)
     *
     * @return int
     */
    public function getBunchSize()
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_BUNCH_SIZE);
    }
}

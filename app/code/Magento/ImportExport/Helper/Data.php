<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * ImportExport data helper
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_ImportExport_Helper_Data extends Magento_Core_Helper_Data
{
    /**#@+
     * XML path for config data
     */
    const XML_PATH_EXPORT_LOCAL_VALID_PATH = 'general/file/importexport_local_valid_paths';
    const XML_PATH_BUNCH_SIZE = 'general/file/bunch_size';
    /**#@-*/

    /**
     * Get maximum upload size message
     *
     * @return string
     */
    public function getMaxUploadSizeMessage()
    {
        $maxImageSize = Mage::getObjectManager()->get('Magento_File_Size')->getMaxFileSizeInMb();
        if ($maxImageSize) {
            $message = __('The total size of the uploadable files can\'t be more that %1M', $maxImageSize);
        } else {
            $message = __('System doesn\'t allow to get file upload settings');
        }
        return $message;
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

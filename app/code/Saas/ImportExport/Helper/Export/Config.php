<?php
/**
 * Saas Export Config Helper
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Helper_Export_Config extends Mage_Core_Helper_Abstract
{
    const DEFAULT_ITEMS_PER_PAGE = 100;

    /**#@+
     * Config keys.
     */
    const XML_PATH_CONFIG_KEY_ENTITIES = 'global/importexport/export_entities';
    /**#@-*/

    /**
     * List of available mime-types
     *
     * @var array
     */
    protected $_mimeTypes = array(
        'csv' => 'text/csv',
    );

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_applicationConfig;

    /**
     * @param Mage_Core_Model_Config $applicationConfig
     */
    public function __construct(
        Mage_Core_Model_Config $applicationConfig
    ) {
        $this->_applicationConfig = $applicationConfig;
    }

    /**
     * @param string $entityType
     * @return int
     */
    public function getItemsPerPage($entityType)
    {
        $items = (int)$this->_applicationConfig
            ->getNode(self::XML_PATH_CONFIG_KEY_ENTITIES . '/' . $entityType . '/per_page');
        return $items ? $items : self::DEFAULT_ITEMS_PER_PAGE;
    }

    /**
     * Retrieve path for export file
     *
     * @param string $entityType
     * @return string
     */
    public function getStorageFilePath($entityType)
    {
        return Mage::getBaseDir('media') . DS . 'importexport' . DS . 'export' . DS . $entityType;
    }

    /**
     * Get mime type by file extension
     *
     * @param string $extension
     * @return string
     */
    public function getMimeTypeByExtension($extension)
    {
        return isset($this->_mimeTypes[$extension]) ? $this->_mimeTypes[$extension] : 'application/octet-stream';
    }
}

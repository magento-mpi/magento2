<?php
/**
 * Saas Export Config Helper
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Helper_Export_Config extends Magento_Core_Helper_Abstract
{
    /**
     * Default items per page
     */
    const DEFAULT_ITEMS_PER_PAGE = 100;

    /**
     * Config value for items per page
     */
    const XML_PATH_CONFIG_KEY_ENTITIES = 'global/importexport/export_entities/%s/per_page';

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_applicationConfig;

    /**
     * @var Magento_Core_Model_Dir
     */
    protected $_dir;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Config $applicationConfig
     * @param Magento_Core_Model_Dir $dir
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Config $applicationConfig,
        Magento_Core_Model_Dir $dir
    ) {
        parent::__construct($context);

        $this->_applicationConfig = $applicationConfig;
        $this->_dir = $dir;
    }

    /**
     * @param string $entityType
     * @return int
     */
    public function getItemsPerPage($entityType)
    {
        $items = (int)$this->_applicationConfig->getNode(sprintf(self::XML_PATH_CONFIG_KEY_ENTITIES, $entityType));
        return $items ? $items : self::DEFAULT_ITEMS_PER_PAGE;
    }

    /**
     * Retrieve directory path for export
     *
     * @return string
     */
    public function getStorageDirectoryPath()
    {
        return $this->_dir->getDir('media') . Magento_Filesystem::DIRECTORY_SEPARATOR . 'importexport'
            . Magento_Filesystem::DIRECTORY_SEPARATOR . 'export';
    }

    /**
     * Retrieve path for export file
     *
     * @param string $entityType
     * @return string
     */
    public function getStorageFilePath($entityType)
    {
        return $this->getStorageDirectoryPath() . Magento_Filesystem::DIRECTORY_SEPARATOR . $entityType;
    }
}

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
    /**
     * Default items per page
     */
    const DEFAULT_ITEMS_PER_PAGE = 100;

    /**#@+
     * Config keys.
     */
    const XML_PATH_CONFIG_KEY_ENTITIES = 'global/importexport/export_entities';
    /**#@-*/

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
}

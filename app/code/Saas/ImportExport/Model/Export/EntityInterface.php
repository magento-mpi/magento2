<?php
/**
 * Entity Export interface. Adaptive interface for export entities from Magento_ImportExport module
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Saas_ImportExport_Model_Export_EntityInterface
{
    /**
     * Set storage for export entity model. Wrapper for setWriter()
     *
     * @param Saas_ImportExport_Model_Export_Adapter_AdapterAbstract $writer
     */
    public function setStorageAdapter(Saas_ImportExport_Model_Export_Adapter_AdapterAbstract $writer);

    /**
     * Get header columns for export data
     *
     * @return array
     */
    public function getHeaderColumns();

    /**
     * Retrieve export collection
     *
     * @return Magento_Data_Collection_Db
     */
    public function getCollection();

    /**
     * Prepare collection for export
     */
    public function prepareCollection();

    /**
     * Export entity collection
     */
    public function exportCollection();
}

<?php
/**
 * Entity Export interface
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
     * @param Saas_ImportExport_Model_Export_Adapter_Abstract $writer
     */
    public function setStorageAdapter(Saas_ImportExport_Model_Export_Adapter_Abstract $writer);

    /**
     * Get header columns for export data
     *
     * @return array
     */
    public function getHeaderColumns();

    /**
     * Retrieve export collection
     *
     * @return Varien_Data_Collection_Db
     */
    public function getCollection();

    /**
     * Prepare collection for export
     *
     * @return mixed
     */
    public function prepareCollection();

    /**
     * Export entity collection
     */
    public function exportCollection();
}

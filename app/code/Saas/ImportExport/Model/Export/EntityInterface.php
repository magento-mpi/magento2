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
     * Set storage for export entity model
     *
     * @param Mage_ImportExport_Model_Export_Adapter_Interface $writer
     * @return mixed
     */
    public function setWriter(Mage_ImportExport_Model_Export_Adapter_Interface $writer);

    /**
     * Get header columns for export data
     *
     * @return array
     */
    public function getHeaderCols();

    /**
     * Retrieve export collection
     *
     * @return Varien_Data_Collection_Db
     */
    public function getCollection();

    /**
     * Export entity collection
     */
    public function exportCollection();
}

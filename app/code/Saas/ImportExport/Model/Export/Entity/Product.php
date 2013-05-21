<?php
/**
 * Adaptive class for export Products entity
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Model_Export_Entity_Product  extends Mage_ImportExport_Model_Export_Entity_Product
    implements Saas_ImportExport_Model_Export_EntityInterface
{
    /**
     * Product entity export constructor
     *
     * @link https://jira.corp.x.com/browse/MAGETWO-9687
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     */
    public function __construct(Mage_Catalog_Model_Resource_Product_Collection $collection)
    {
        $this->_indexValueAttributes = array_merge($this->_indexValueAttributes, array(
            'unit_price_unit',
            'unit_price_base_unit',
        ));
        parent::__construct($collection);
    }

    /**
     * {@inheritdoc}
     */
    public function prepareCollection()
    {
        $this->_prepareEntityCollection($this->_getEntityCollection());
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        return $this->_getEntityCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderColumns()
    {
        $headerCols = $this->_getHeaderColumns();
        //  collect header columns
        if (!$headerCols) {
            $this->_getExportData();
        }
        return $this->_getHeaderColumns();
    }

    /**
     * {@inheritdoc}
     */
    public function setStorageAdapter(Saas_ImportExport_Model_Export_Adapter_AdapterAbstract $writer)
    {
        $this->_writer = $writer;
    }

    /**
     * {@inheritdoc}
     */
    public function exportCollection()
    {
        $writer = $this->getWriter();
        foreach ($this->_getExportData() as $dataRow) {
            $writer->writeRow($dataRow);
        }
    }
}

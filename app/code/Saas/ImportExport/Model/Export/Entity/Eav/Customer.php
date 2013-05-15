<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Model_Export_Entity_Eav_Customer extends Mage_ImportExport_Model_Export_Entity_Eav_Customer
    implements Saas_ImportExport_Model_Export_EntityInterface
{
    /**
     * Collection flag status
     *
     * @var bool
     */
    protected $_isCollectionInitialized = false;

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        if (!$this->_isCollectionInitialized) {
            $this->_isCollectionInitialized = true;
            $this->_prepareEntityCollection($this->_customerCollection);
        }
        return $this->_customerCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderCols()
    {
        $validAttributeCodes = $this->_getExportAttributeCodes();
        return array_merge($this->_permanentAttributes, $validAttributeCodes, array('password'));
    }

    /**
     * {@inheritdoc}
     */
    public function exportCollection()
    {
        $collection = $this->getCollection();
        foreach ($collection as $item) {
            $this->exportItem($item);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setStorageAdapter(Saas_ImportExport_Model_Export_Adapter_Abstract $writer)
    {
        $this->_writer = $writer;
    }
}

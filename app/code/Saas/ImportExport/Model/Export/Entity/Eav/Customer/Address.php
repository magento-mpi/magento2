<?php
class Saas_ImportExport_Model_Export_Entity_Eav_Customer_Address
    extends Mage_ImportExport_Model_Export_Entity_Eav_Customer_Address
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
            $this->_prepareEntityCollection($this->_addressCollection);
            $this->_addressCollection->setCustomerFilter(array_keys($this->_customers));
        }
        return $this->_addressCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderCols()
    {
        return array_merge(
            $this->_permanentAttributes,
            $this->_getExportAttributeCodes(),
            array_keys(self::$_defaultAddressAttributeMapping)
        );
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

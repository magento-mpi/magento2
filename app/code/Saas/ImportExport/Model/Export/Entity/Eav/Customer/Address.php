<?php
class Saas_ImportExport_Model_Export_Entity_Eav_Customer_Address
    extends Mage_ImportExport_Model_Export_Entity_Eav_Customer_Address
    implements Saas_ImportExport_Model_Export_EntityInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        return $this->_getEntityCollection();
    }

    /**
     * @inheritdoc
     */
    public function prepareCollection()
    {
        $this->_prepareEntityCollection($this->_getEntityCollection());
        $this->_getEntityCollection()->setCustomerFilter(array_keys($this->_customers));
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderColumns()
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
        foreach ($this->getCollection() as $item) {
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

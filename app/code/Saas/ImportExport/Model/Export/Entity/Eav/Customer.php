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
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderColumns()
    {
        return $this->_getHeaderColumns();
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

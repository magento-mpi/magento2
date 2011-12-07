<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Dataflow Batch abstract resource model
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Dataflow_Model_Resource_Batch_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Retrieve Id collection
     *
     * @param Mage_Dataflow_Model_Batch_Abstract $object
     * @return array
     */
    public function getIdCollection(Mage_Dataflow_Model_Batch_Abstract $object)
    {
        if (!$object->getBatchId()) {
            return array();
        }

        $ids = array();
        $select = $this->_getWriteAdapter()->select()
            ->from($this->getMainTable(), array($this->getIdFieldName()))
            ->where('batch_id = :batch_id');
        $ids = $this->_getWriteAdapter()->fetchCol($select, array('batch_id' => $object->getBatchId()));
        return $ids;
    }

    /**
     * Delete current Batch collection
     *
     * @param Mage_Dataflow_Model_Batch_Abstract $object
     * @return Mage_Dataflow_Model_Resource_Batch_Abstract
     */
    public function deleteCollection(Mage_Dataflow_Model_Batch_Abstract $object)
    {
        if (!$object->getBatchId()) {
            return $this;
        }

        $this->_getWriteAdapter()->delete($this->getMainTable(), array('batch_id=?' => $object->getBatchId()));
        return $this;
    }
}

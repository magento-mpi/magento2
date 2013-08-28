<?php
/**
 * Google Experiment Code resource model
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_GoogleOptimizer_Model_Resource_Code extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('googleoptimizer_code', 'code_id');
    }

    /**
     * Load scripts by entity and store
     *
     * @param Magento_GoogleOptimizer_Model_Code $object
     * @param int $entityId
     * @param string $entityType
     * @param int $storeId
     * @return Magento_GoogleOptimizer_Model_Resource_Code
     */
    public function loadByEntityType($object, $entityId, $entityType, $storeId)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from(
                array('t_def' => $this->getMainTable()),
                array('entity_id', 'entity_type', 'experiment_script', 'code_id'))
            ->where('t_def.entity_id=?', $entityId)
            ->where('t_def.entity_type=?', $entityType)
            ->where('t_def.store_id IN (0, ?)', $storeId)
            ->order('t_def.store_id DESC')
            ->limit(1);

        $data = $adapter->fetchRow($select);

        if ($data) {
            $object->setData($data);
        }
        $this->_afterLoad($object);
        return $this;
    }
}

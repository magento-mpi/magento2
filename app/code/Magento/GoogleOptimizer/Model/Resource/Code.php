<?php
/**
 * Google Experiment Code resource model
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\GoogleOptimizer\Model\Resource;

class Code extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('googleoptimizer_code', 'code_id');
    }

    /**
     * Load scripts by entity and store
     *
     * @param \Magento\GoogleOptimizer\Model\Code $object
     * @param int $entityId
     * @param string $entityType
     * @param int $storeId
     * @return $this
     */
    public function loadByEntityType($object, $entityId, $entityType, $storeId)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()->from(
            array('t_def' => $this->getMainTable()),
            array('entity_id', 'entity_type', 'experiment_script', 'code_id')
        )->where(
            't_def.entity_id=?',
            $entityId
        )->where(
            't_def.entity_type=?',
            $entityType
        )->where(
            't_def.store_id IN (0, ?)',
            $storeId
        )->order(
            't_def.store_id DESC'
        )->limit(
            1
        );

        $data = $adapter->fetchRow($select);

        if ($data) {
            $object->setData($data);
        }
        $this->_afterLoad($object);
        return $this;
    }
}

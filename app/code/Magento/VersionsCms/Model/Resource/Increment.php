<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Model\Resource;

/**
 * Increment resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Increment extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_versionscms_increment', 'increment_id');
    }

    /**
     * Load increment counter by passed node and level
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param int $type
     * @param int $node
     * @param int $level
     * @return bool
     */
    public function loadByTypeNodeLevel(\Magento\Framework\Model\AbstractModel $object, $type, $node, $level)
    {
        $read = $this->_getReadAdapter();

        $select = $read->select()->from(
            $this->getMainTable()
        )->forUpdate(
            true
        )->where(
            implode(
                ' AND ',
                [
                    'increment_type  = :increment_type',
                    'increment_node  = :increment_node',
                    'increment_level = :increment_level'
                ]
            )
        );

        $bind = [':increment_type' => $type, ':increment_node' => $node, ':increment_level' => $level];

        $data = $read->fetchRow($select, $bind);

        if (!$data) {
            return false;
        }

        $object->setData($data);

        $this->_afterLoad($object);

        return true;
    }

    /**
     * Remove unneeded increment record.
     *
     * @param int $type
     * @param int $node
     * @param int $level
     * @return $this
     */
    public function cleanIncrementRecord($type, $node, $level)
    {
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            ['increment_type = ?' => $type, 'increment_node = ?' => $node, 'increment_level = ?' => $level]
        );

        return $this;
    }
}

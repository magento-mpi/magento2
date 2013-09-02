<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Increment resource model
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_VersionsCms_Model_Resource_Increment extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('magento_versionscms_increment', 'increment_id');
    }

    /**
     * Load increment counter by passed node and level
     *
     * @param Magento_Core_Model_Abstract $object
     * @param int $type
     * @param int $node
     * @param int $level
     * @return bool
     */
    public function loadByTypeNodeLevel(Magento_Core_Model_Abstract $object, $type, $node, $level)
    {
        $read = $this->_getReadAdapter();

        $select = $read->select()->from($this->getMainTable())
            ->forUpdate(true)
            ->where(implode(' AND ', array(
                'increment_type  = :increment_type',
                'increment_node  = :increment_node',
                'increment_level = :increment_level'
             )));

        $bind = array(':increment_type'  => $type,
                      ':increment_node'  => $node,
                      ':increment_level' => $level);

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
     * @return Magento_VersionsCms_Model_Resource_Increment
     */
    public function cleanIncrementRecord($type, $node, $level)
    {
        $this->_getWriteAdapter()->delete($this->getMainTable(),
            array('increment_type = ?'  => $type,
                  'increment_node = ?'  => $node,
                  'increment_level = ?' => $level));

        return $this;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Model\Resource;

/**
 * Gift registry entity registrants resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Person extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Resource model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_giftregistry_person', 'person_id');
    }

    /**
     * Serialization for custom attributes
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setCustomValues(serialize($object->getCustom()));
        return parent::_beforeSave($object);
    }

    /**
     * De-serialization for custom attributes
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setCustom(unserialize($object->getCustomValues()));
        return parent::_afterLoad($object);
    }

    /**
     * Delete orphan persons
     *
     * @param int $entityId
     * @param array $personLeft - records which should not be deleted
     * @return $this
     */
    public function deleteOrphan($entityId, $personLeft = [])
    {
        $adapter = $this->_getWriteAdapter();
        $condition = [];
        $conditionIn = [];

        $condition[] = $adapter->quoteInto('entity_id = ?', (int)$entityId);
        if (is_array($personLeft) && !empty($personLeft)) {
            $condition[] = $adapter->quoteInto('person_id NOT IN (?)', $personLeft);
        }
        $adapter->delete($this->getMainTable(), $condition);

        return $this;
    }
}

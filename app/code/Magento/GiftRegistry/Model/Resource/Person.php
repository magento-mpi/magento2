<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Gift registry entity registrants resource model
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftRegistry\Model\Resource;

class Person extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Resource model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('magento_giftregistry_person', 'person_id');
    }

    /**
     * Serialization for custom attributes
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @return \Magento\Core\Model\Resource\Db\AbstractDb
     */
    protected function _beforeSave(\Magento\Core\Model\AbstractModel $object)
    {
        $object->setCustomValues(serialize($object->getCustom()));
        return parent::_beforeSave($object);
    }

    /**
     * De-serialization for custom attributes
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @return \Magento\Core\Model\Resource\Db\AbstractDb
     */
    protected function _afterLoad(\Magento\Core\Model\AbstractModel $object)
    {
        $object->setCustom(unserialize($object->getCustomValues()));
        return parent::_afterLoad($object);
    }

    /**
     * Delete orphan persons
     *
     * @param int $entityId
     * @param array $personLeft - records which should not be deleted
     * @return \Magento\GiftRegistry\Model\Resource\Person
     */
    public function deleteOrphan($entityId, $personLeft = array())
    {
        $adapter     = $this->_getWriteAdapter();
        $condition   = array();
        $conditionIn = array();

        $condition[] = $adapter->quoteInto('entity_id = ?', (int)$entityId);
        if (is_array($personLeft) && !empty($personLeft)) {
            $condition[] = $adapter->quoteInto('person_id NOT IN (?)', $personLeft);
        }
        $adapter->delete($this->getMainTable(), $condition);

        return $this;
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Gift registry entity registrants resource model
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftRegistry_Model_Resource_Person extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_giftregistry_person', 'person_id');
    }

    /**
     * Serialization for custom attributes
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Core_Model_Resource_Db_Abstract
     */
    protected function _beforeSave(Magento_Core_Model_Abstract $object)
    {
        $object->setCustomValues(serialize($object->getCustom()));
        return parent::_beforeSave($object);
    }

    /**
     * De-serialization for custom attributes
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Core_Model_Resource_Db_Abstract
     */
    protected function _afterLoad(Magento_Core_Model_Abstract $object)
    {
        $object->setCustom(unserialize($object->getCustomValues()));
        return parent::_afterLoad($object);
    }

    /**
     * Delete orphan persons
     *
     * @param int $entityId
     * @param array $personLeft - records which should not be deleted
     * @return Enterprise_GiftRegistry_Model_Resource_Person
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

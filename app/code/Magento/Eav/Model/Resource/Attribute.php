<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * EAV attribute resource model (Using Forms)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Eav\Model\Resource;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DB\Select;

abstract class Attribute extends \Magento\Eav\Model\Resource\Entity\Attribute
{
    /**
     * Get EAV website table
     *
     * Get table, where website-dependent attribute parameters are stored
     * If realization doesn't demand this functionality, let this function just return null
     *
     * @return string|null
     */
    abstract protected function _getEavWebsiteTable();

    /**
     * Get Form attribute table
     *
     * Get table, where dependency between form name and attribute ids are stored
     *
     * @return string|null
     */
    abstract protected function _getFormAttributeTable();

    /**
     * Perform actions before object save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $validateRules = $object->getData('validate_rules');
        if (is_array($validateRules)) {
            $object->setData('validate_rules', serialize($validateRules));
        }
        return parent::_beforeSave($object);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param AbstractModel $object
     * @return Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $websiteId = (int)$object->getWebsite()->getId();
        if ($websiteId) {
            $adapter = $this->_getReadAdapter();
            $columns = array();
            $scopeTable = $this->_getEavWebsiteTable();
            $describe = $adapter->describeTable($scopeTable);
            unset($describe['attribute_id']);
            foreach (array_keys($describe) as $columnName) {
                $columns['scope_' . $columnName] = $columnName;
            }
            $conditionSql = $adapter->quoteInto(
                $this->getMainTable() . '.attribute_id = scope_table.attribute_id AND scope_table.website_id =?',
                $websiteId
            );
            $select->joinLeft(array('scope_table' => $scopeTable), $conditionSql, $columns);
        }

        return $select;
    }

    /**
     * Save attribute/form relations after attribute save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(AbstractModel $object)
    {
        $forms = $object->getData('used_in_forms');
        $adapter = $this->_getWriteAdapter();
        if (is_array($forms)) {
            $where = array('attribute_id=?' => $object->getId());
            $adapter->delete($this->_getFormAttributeTable(), $where);

            $data = array();
            foreach ($forms as $formCode) {
                $data[] = array('form_code' => $formCode, 'attribute_id' => (int)$object->getId());
            }

            if ($data) {
                $adapter->insertMultiple($this->_getFormAttributeTable(), $data);
            }
        }

        // update sort order
        if (!$object->isObjectNew() && $object->dataHasChangedFor('sort_order')) {
            $data = array('sort_order' => $object->getSortOrder());
            $where = array('attribute_id=?' => (int)$object->getId());
            $adapter->update($this->getTable('eav_entity_attribute'), $data, $where);
        }

        // save scope attributes
        $websiteId = (int)$object->getWebsite()->getId();
        if ($websiteId) {
            $table = $this->_getEavWebsiteTable();
            $describe = $this->_getReadAdapter()->describeTable($table);
            $data = array();
            if (!$object->getScopeWebsiteId() || $object->getScopeWebsiteId() != $websiteId) {
                $data = $this->getScopeValues($object);
            }

            $data['attribute_id'] = (int)$object->getId();
            $data['website_id'] = (int)$websiteId;
            unset($describe['attribute_id']);
            unset($describe['website_id']);

            $updateColumns = array();
            foreach (array_keys($describe) as $columnName) {
                $data[$columnName] = $object->getData('scope_' . $columnName);
                $updateColumns[] = $columnName;
            }

            $adapter->insertOnDuplicate($table, $data, $updateColumns);
        }

        return parent::_afterSave($object);
    }

    /**
     * Return scope values for attribute and website
     *
     * @param \Magento\Eav\Model\Attribute $object
     * @return array
     */
    public function getScopeValues(\Magento\Eav\Model\Attribute $object)
    {
        $adapter = $this->_getReadAdapter();
        $bind = array('attribute_id' => (int)$object->getId(), 'website_id' => (int)$object->getWebsite()->getId());
        $select = $adapter->select()->from(
            $this->_getEavWebsiteTable()
        )->where(
            'attribute_id = :attribute_id'
        )->where(
            'website_id = :website_id'
        )->limit(
            1
        );
        $result = $adapter->fetchRow($select, $bind);

        if (!$result) {
            $result = array();
        }

        return $result;
    }

    /**
     * Return forms in which the attribute
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return array
     */
    public function getUsedInForms(AbstractModel $object)
    {
        $adapter = $this->_getReadAdapter();
        $bind = array('attribute_id' => (int)$object->getId());
        $select = $adapter->select()->from(
            $this->_getFormAttributeTable(),
            'form_code'
        )->where(
            'attribute_id = :attribute_id'
        );

        return $adapter->fetchCol($select, $bind);
    }
}

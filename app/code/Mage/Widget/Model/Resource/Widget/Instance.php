<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Widget Instance Resource Model
 *
 * @category    Mage
 * @package     Mage_Widget
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Widget_Model_Resource_Widget_Instance extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('widget_instance', 'instance_id');
    }

    /**
     * Perform actions after object load
     *
     * @param Mage_Widget_Model_Widget_Instance $object
     * @return Mage_Widget_Model_Resource_Widget_Instance
     */
    protected function _afterLoad(Magento_Core_Model_Abstract $object)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getTable('widget_instance_page'))
            ->where('instance_id = ?', (int)$object->getId());
        $result = $adapter->fetchAll($select);
        $object->setData('page_groups', $result);
        return parent::_afterLoad($object);
    }

    /**
     * Perform actions after object save
     *
     * @param Mage_Widget_Model_Widget_Instance $object
     * @return Mage_Widget_Model_Resource_Widget_Instance
     */
    protected function _afterSave(Magento_Core_Model_Abstract $object)
    {
        $pageTable         = $this->getTable('widget_instance_page');
        $pageLayoutTable   = $this->getTable('widget_instance_page_layout');
        $readAdapter       = $this->_getReadAdapter();
        $writeAdapter      = $this->_getWriteAdapter();

        $select = $readAdapter->select()
            ->from($pageTable, 'page_id')
            ->where('instance_id = ?', (int)$object->getId());
        $pageIds = $readAdapter->fetchCol($select);

        $removePageIds = array_diff($pageIds, $object->getData('page_group_ids'));

        if (is_array($pageIds) && count($pageIds) > 0) {
            $inCond = $readAdapter->prepareSqlCondition('page_id', array('in' => $pageIds));

            $select = $readAdapter->select()
                ->from($pageLayoutTable, 'layout_update_id')
                ->where($inCond);
            $removeLayoutUpdateIds = $readAdapter->fetchCol($select);

            $writeAdapter->delete($pageLayoutTable, $inCond);
            $this->_deleteLayoutUpdates($removeLayoutUpdateIds);
        }

        $this->_deleteWidgetInstancePages($removePageIds);

        foreach ($object->getData('page_groups') as $pageGroup) {
            $pageLayoutUpdateIds = $this->_saveLayoutUpdates($object, $pageGroup);
            $data = array(
                'page_group'      => $pageGroup['group'],
                'layout_handle'   => $pageGroup['layout_handle'],
                'block_reference' => $pageGroup['block_reference'],
                'page_for'        => $pageGroup['for'],
                'entities'        => $pageGroup['entities'],
                'page_template'   => $pageGroup['template'],
            );
            $pageId = $pageGroup['page_id'];
            if (in_array($pageGroup['page_id'], $pageIds)) {
                $writeAdapter->update($pageTable, $data, array('page_id = ?' => (int)$pageId));
            } else {
                $writeAdapter->insert($pageTable,
                    array_merge(array('instance_id' => $object->getId()),
                    $data));
                $pageId = $writeAdapter->lastInsertId($pageTable);
            }
            foreach ($pageLayoutUpdateIds as $layoutUpdateId) {
                $writeAdapter->insert($pageLayoutTable, array(
                    'page_id' => $pageId,
                    'layout_update_id' => $layoutUpdateId
                ));
            }
        }

        return parent::_afterSave($object);
    }

    /**
     * Prepare and save layout updates data
     *
     * @param Mage_Widget_Model_Widget_Instance $widgetInstance
     * @param array $pageGroupData
     * @return array of inserted layout updates ids
     */
    protected function _saveLayoutUpdates($widgetInstance, $pageGroupData)
    {
        $writeAdapter          = $this->_getWriteAdapter();
        $pageLayoutUpdateIds   = array();
        $storeIds              = $this->_prepareStoreIds($widgetInstance->getStoreIds());
        $layoutUpdateTable     = $this->getTable('core_layout_update');
        $layoutUpdateLinkTable = $this->getTable('core_layout_link');

        foreach ($pageGroupData['layout_handle_updates'] as $handle) {
            $xml = $widgetInstance->generateLayoutUpdateXml(
                $pageGroupData['block_reference'],
                $pageGroupData['template']
            );
            $insert = array(
                    'handle'     => $handle,
                    'xml'        => $xml
            );
            if (strlen($widgetInstance->getSortOrder())) {
                $insert['sort_order'] = $widgetInstance->getSortOrder();
            };

            $writeAdapter->insert($layoutUpdateTable, $insert);
            $layoutUpdateId = $writeAdapter->lastInsertId($layoutUpdateTable);
            $pageLayoutUpdateIds[] = $layoutUpdateId;

            $data = array();
            foreach ($storeIds as $storeId) {
                $data[] = array(
                    'store_id'         => $storeId,
                    'theme_id'         => $widgetInstance->getThemeId(),
                    'layout_update_id' => $layoutUpdateId);
            }
            $writeAdapter->insertMultiple($layoutUpdateLinkTable, $data);
        }
        return $pageLayoutUpdateIds;
    }

    /**
     * Prepare store ids.
     * If one of store id is default (0) return all store ids
     *
     * @param array $storeIds
     * @return array
     */
    protected function _prepareStoreIds($storeIds)
    {
        if (in_array('0', $storeIds)) {
            $storeIds = array(0);
        }
        return $storeIds;
    }

    /**
     * Perform actions before object delete.
     * Collect page ids and layout update ids and set to object for further delete
     *
     * @param Magento_Object $object
     * @return Mage_Widget_Model_Resource_Widget_Instance
     */
    protected function _beforeDelete(Magento_Core_Model_Abstract $object)
    {
        $writeAdapter = $this->_getWriteAdapter();
        $select = $writeAdapter->select()
            ->from(array('main_table' => $this->getTable('widget_instance_page')), array())
            ->joinInner(
                array('layout_page_table' => $this->getTable('widget_instance_page_layout')),
                'layout_page_table.page_id = main_table.page_id',
                array('layout_update_id')
            )
            ->where('main_table.instance_id=?', $object->getId());
        $result = $writeAdapter->fetchCol($select);
        $object->setLayoutUpdateIdsToDelete($result);
        return $this;
    }

    /**
     * Perform actions after object delete.
     * Delete layout updates by layout update ids collected in _beforeSave
     *
     * @param Mage_Widget_Model_Widget_Instance $object
     * @return Mage_Widget_Model_Resource_Widget_Instance
     */
    protected function _afterDelete(Magento_Core_Model_Abstract $object)
    {
        $this->_deleteLayoutUpdates($object->getLayoutUpdateIdsToDelete());
        return parent::_afterDelete($object);
    }

    /**
     * Delete widget instance pages by given ids
     *
     * @param array $pageIds
     * @return Mage_Widget_Model_Resource_Widget_Instance
     */
    protected function _deleteWidgetInstancePages($pageIds)
    {
        $writeAdapter = $this->_getWriteAdapter();
        if ($pageIds) {
            $inCond = $writeAdapter->prepareSqlCondition('page_id', array(
                'in' => $pageIds
            ));
            $writeAdapter->delete(
                $this->getTable('widget_instance_page'),
                $inCond
            );
        }
        return $this;
    }

    /**
     * Delete layout updates by given ids
     *
     * @param array $layoutUpdateIds
     * @return Mage_Widget_Model_Resource_Widget_Instance
     */
    protected function _deleteLayoutUpdates($layoutUpdateIds)
    {
        $writeAdapter = $this->_getWriteAdapter();
        if ($layoutUpdateIds) {
            $inCond = $writeAdapter->prepareSqlCondition('layout_update_id', array(
                'in' => $layoutUpdateIds
            ));
            $writeAdapter->delete(
                $this->getTable('core_layout_update'),
                $inCond
            );
        }
        return $this;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'store_ids')
            ->where("{$this->getIdFieldName()} = ?", (int)$id);
        $storeIds = $adapter->fetchOne($select);
        return $storeIds ? explode(',', $storeIds) : array();
    }

}

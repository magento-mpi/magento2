<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Cms Widget Instance Resource Model
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Cms_Model_Mysql4_Widget_Instance extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_handlesToCleanCache = array();

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('enterprise_cms/widget_instance', 'instance_id');
    }

    /**
     * Reset handles to clean in cache
     *
     * @return Enterprise_Cms_Model_Mysql4_Widget_Instance
     */
    public function resetHandlesToCleanCache()
    {
        $this->_handlesToCleanCache = array();
        return $this;
    }

    /**
     * Setter
     *
     * @param array $handles
     * @return Enterprise_Cms_Model_Mysql4_Widget_Instance
     */
    public function setHandlesToCleanCache($handles)
    {
        $this->_handlesToCleanCache = $handles;
        return $this;
    }

    /**
     * Add handle to clean in cache
     *
     * @param string $handle
     * @return Enterprise_Cms_Model_Mysql4_Widget_Instance
     */
    public function addHandleToCleanCache($handle)
    {
        $this->_handlesToCleanCache[] = $handle;
        return $this;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getHandlesToCleanCache()
    {
        return $this->_handlesToCleanCache;
    }

    /**
     * Perform actions after object load
     *
     * @param Enterprise_Cms_Model_Widget_Instance $object
     * @return Enterprise_Cms_Model_Mysql4_Widget_Instance
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('enterprise_cms/widget_instance_page'))
            ->where('instance_id = ?', $object->getId());
        $object->setData('page_groups', $this->_getReadAdapter()->fetchAll($select));
        return parent::_afterLoad($object);
    }

    /**
     * Perform actions after object save
     *
     * @param Enterprise_Cms_Model_Widget_Instance $object
     * @return Enterprise_Cms_Model_Mysql4_Widget_Instance
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $this->resetHandlesToCleanCache();

        $pageTable = $this->getTable('enterprise_cms/widget_instance_page');
        $pageLayoutTable = $this->getTable('enterprise_cms/widget_instance_page_layout');
        $layoutUpdateTable = $this->getTable('core/layout_update');
        $layoutLinkTable = $this->getTable('core/layout_link');
        $write = $this->_getWriteAdapter();

        $select = $write->select()
            ->from($pageTable, array('page_id'))
            ->where('instance_id = ?', $object->getId());
        $pageIds = $write->fetchCol($select);

        $removePageIds = array_diff($pageIds, $object->getData('page_group_ids'));

        $select = $write->select()
            ->from($pageLayoutTable, array('layout_update_id'))
            ->where('page_id in (?)', $pageIds);
        $removeLayoutUpdateIds = $write->fetchCol($select);

        $select = $write->select()
            ->from($layoutUpdateTable, array('handle'))
            ->where('layout_update_id in (?)', $removeLayoutUpdateIds);
        $this->setHandlesToCleanCache($write->fetchCol($select));

        $this->_deleteWidgetInstancePages($removePageIds);
        $write->delete($pageLayoutTable, $write->quoteInto('page_id in (?)', $pageIds));
        $this->_deleteLayoutUpdates($removeLayoutUpdateIds);

        foreach ($object->getData('page_groups') as $pageGroup) {
            $pageLayoutUpdateIds = $this->_saveLayoutUpdates($object, $pageGroup);
            $data = array(
                'group' => $pageGroup['group'],
                'layout_handle' => $pageGroup['layout_handle'],
                'block_reference' => $pageGroup['block_reference'],
                'for' => $pageGroup['for'],
                'entities' => $pageGroup['entities'],
                'position' => $pageGroup['position']
            );
            $pageId = $pageGroup['page_id'];
            if (in_array($pageGroup['page_id'], $pageIds)) {
                $write->update($pageTable, $data, $write->quoteInto('page_id = ?', $pageId));
            } else {
                $write->insert($pageTable,
                    array_merge(array(
                        'instance_id' => $object->getId()),$data
                ));
                $pageId = $write->lastInsertId();
            }
            foreach ($pageLayoutUpdateIds as $layoutUpdateId) {
                $write->insert($pageLayoutTable, array(
                    'page_id' => $pageId,
                    'layout_update_id' => $layoutUpdateId
                ));
            }
        }

        $this->_cleanLayoutCache();

        return parent::_afterSave($object);
    }

    /**
     * Prepare and save layout updates data
     *
     * @param Enterprise_Cms_Model_Widget_Instance $widgetInstance
     * @param array $pageGroupData
     * @return array of inserted layout updates ids
     */
    protected function _saveLayoutUpdates($widgetInstance, $pageGroupData)
    {
        $write = $this->_getWriteAdapter();
        $pageLayoutUpdateIds = array();
        $storeIds = $this->_prepareStoreIds($widgetInstance->getStoreIds());
        foreach ($pageGroupData['layout_handle_updates'] as $handle) {
            $this->_getWriteAdapter()->insert(
                $this->getTable('core/layout_update'), array(
                    'handle' => $handle,
                    'xml' => $this->_prepareLayoutXml(
                        $handle,
                        $pageGroupData['block_reference'],
                        $widgetInstance->getType(),
                        $widgetInstance->getWidgetParameters(),
                        $pageGroupData['position'])
            ));
            $layoutUpdateId = $this->_getWriteAdapter()->lastInsertId();
            $this->addHandleToCleanCache($handle);
            $pageLayoutUpdateIds[] = $layoutUpdateId;
            foreach ($storeIds as $storeId) {
                $this->_getWriteAdapter()->insert(
                    $this->getTable('core/layout_link'), array(
                        'store_id'         => $storeId,
                        'package'          => $widgetInstance->getPackage(),
                        'theme'            => $widgetInstance->getTheme(),
                        'layout_update_id' => $layoutUpdateId
                ));
            }
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
        if (in_array(0, $storeIds)) {
            $storeIds = array_keys(Mage::app()->getStores(false));
        }
        return $storeIds;
    }

    /**
     * Generate xml layout update
     *
     * @param string $layoutHandle
     * @param string $blockReference
     * @param string $widgetType
     * @param array $properties
     * @return string
     */
    protected function _prepareLayoutXml($layoutHandle, $blockReference, $widgetType, $properties, $position = 'before')
    {
        $xml = '<reference name="'.$blockReference.'">';
        $xml .= '<block type="'.$widgetType.'" name="'.md5(microtime()).'" '.$position.'="-">';
        foreach ($properties as $propertyName => $propertyValue) {
            $xml .= '<action method="setData"><name>'.$propertyName.'</name><value>'.$propertyValue.'</value></action>';
        }
        $xml .= '</block></reference>';
        return $xml;
    }

    /**
     * Perform actions before object delete.
     * Collect page ids and layout update ids and set to object for further delete
     *
     * @param Varien_Object $object
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from(array('main_table' => $this->getTable('enterprise_cms/widget_instance_page')), array())
            ->joinInner(
                array('layout_page_table' => $this->getTable('enterprise_cms/widget_instance_page_layout')),
                'layout_page_table.page_id = main_table.page_id',
                array('layout_page_table.layout_update_id')
            )
            ->where('main_table.instance_id = ?', $object->getId());
        $object->setLayoutUpdateIdsToDelete($this->_getWriteAdapter()->fetchCol($select));
        return $this;
    }

    /**
     * Perform actions after object delete.
     * Delete layout updates by layout update ids collected in _beforeSave
     * and clean cache
     *
     * @param Enterprise_Cms_Model_Widget_Instance $object
     * @return Enterprise_Cms_Model_Mysql4_Widget_Instance
     */
    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        $this->resetHandlesToCleanCache();

        $select = $this->_getWriteAdapter()->select()
            ->from($this->getTable('core/layout_update'), array('handle'))
            ->where('layout_update_id in (?)', $object->getLayoutUpdateIdsToDelete());
        $this->setHandlesToCleanCache($this->_getWriteAdapter()->fetchCol($select));

        $this->_deleteLayoutUpdates($object->getLayoutUpdateIdsToDelete());

        $this->_cleanLayoutCache();

        return parent::_afterDelete($object);
    }

    /**
     * Delete widget instance pages by given ids
     *
     * @param array $pageIds
     * @return Enterprise_Cms_Model_Mysql4_Widget_Instance
     */
    protected function _deleteWidgetInstancePages($pageIds)
    {
        $this->_getWriteAdapter()->delete(
            $this->getTable('enterprise_cms/widget_instance_page'),
            $this->_getWriteAdapter()->quoteInto('page_id in (?)', $pageIds)
        );
        $this->_getWriteAdapter()->delete(
            $this->getTable('enterprise_cms/widget_instance_page_layout'),
            $this->_getWriteAdapter()->quoteInto('page_id in (?)', $pageIds)
        );
        return $this;
    }

    /**
     * Delete layout updates by given ids
     *
     * @param array $layoutUpdateIds
     * @return Enterprise_Cms_Model_Mysql4_Widget_Instance
     */
    protected function _deleteLayoutUpdates($layoutUpdateIds)
    {
        $this->_getWriteAdapter()->delete(
            $this->getTable('core/layout_update'),
            $this->_getWriteAdapter()->quoteInto('layout_update_id in (?)', $layoutUpdateIds)
        );
        $this->_getWriteAdapter()->delete(
            $this->getTable('core/layout_link'),
            $this->_getWriteAdapter()->quoteInto('layout_update_id in (?)', $layoutUpdateIds)
        );
        return $this;
    }

    /**
     * Clean cache by handles
     *
     * @return Enterprise_Cms_Model_Mysql4_Widget_Instance
     */
    protected function _cleanLayoutCache()
    {
        $handles = $this->getHandlesToCleanCache();
        if (!empty($handles) && Mage::app()->useCache('layout')) {
            Mage::app()->cleanCache($handles);
        }
        return $this;
    }
}
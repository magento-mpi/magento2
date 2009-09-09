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
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('enterprise_cms/widget_instance', 'instance_id');
    }

    /**
     * Perform actions after object load
     *
     * @param Varien_Object $object
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('enterprise_cms/widget_instance_page'))
            ->where('instance_id = ?', $object->getId());
        $object->setData('page_groups', $this->_getReadAdapter()->fetchAll($select));
        return $this;
    }

    /**
     * Perform actions after object save
     *
     * @param Varien_Object $object
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $pageTable = $this->getTable('enterprise_cms/widget_instance_page');
        $pageLayoutTable = $this->getTable('enterprise_cms/widget_instance_page_layout');
        $layoutUpdateTable = $this->getTable('core/layout_update');
        $layoutLinkTable = $this->getTable('core/layout_link');
        $write = $this->_getWriteAdapter();
        $pageIds = array();
        $removePageIds = array();
        $removeLayoutUpdateIds = array();
        $select = $write->select()
            ->from($pageTable, array('page_id'))
            ->where('instance_id = ?', $object->getId());
        foreach ($write->fetchAll($select) as $row) {
            if (!in_array($row['page_id'], $object->getData('page_group_ids'))) {
                $removePageIds[] = $row['page_id'];
            }
            $pageIds[] = $row['page_id'];
        }
        $select = $write->select()
            ->from($pageLayoutTable, array('layout_update_id'))
            ->where('page_id in (?)', $pageIds);
        foreach ($write->fetchAll($select) as $row) {
            $removeLayoutUpdateIds[] = $row['layout_update_id'];
        }
        $write->delete($pageTable, $write->quoteInto('page_id in (?)', $removePageIds));
        $write->delete($pageLayoutTable, $write->quoteInto('page_id in (?)', $pageIds));
        $write->delete($layoutUpdateTable, $write->quoteInto('layout_update_id in (?)', $removeLayoutUpdateIds));
        $write->delete($layoutLinkTable, $write->quoteInto('layout_update_id in (?)', $removeLayoutUpdateIds));
        foreach ($object->getData('page_groups') as $pageGroup) {
            $pageLayoutUpdateIds = $this->_saveLayoutUpdates($object, $pageGroup);
            $data = array(
                'group' => $pageGroup['group'],
                'layout_handle' => $pageGroup['layout_handle'],
                'block_reference' => $pageGroup['block_reference'],
                'for' => $pageGroup['for'],
                'entities' => $pageGroup['entities']
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
        $layoutHandle = array();
        if ($pageGroupData['for'] == Enterprise_Cms_Model_Widget_Instance::SPECIFIC_ENTITIES) {
            foreach (explode(',', $pageGroupData['entities']) as $entity) {
                $layoutHandle[] = str_replace('{{ID}}', $entity, $pageGroupData['specific_layout_handle']);
            }
        } else {
            $layoutHandle = array($pageGroupData['layout_handle']);
        }
        $pageLayoutUpdateIds = array();
        $storeIds = $this->_prepareStoreIds($widgetInstance->getStoreIds());
        foreach ($layoutHandle as $handle) {
            $this->_getWriteAdapter()->insert(
                $this->getTable('core/layout_update'), array(
                    'handle' => $handle,
                    'xml' => $this->_prepareLayoutXml(
                        $handle,
                        $pageGroupData['block_reference'],
                        $widgetInstance->getType(),
                        $widgetInstance->getWidgetParameters())
            ));
            $layoutUpdateId = $this->_getWriteAdapter()->lastInsertId();
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
}
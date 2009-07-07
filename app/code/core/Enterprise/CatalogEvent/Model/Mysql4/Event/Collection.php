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
 * @package    Enterprise_CatalogEvent
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Catalog Event resource collection
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 */
class Enterprise_CatalogEvent_Model_Mysql4_Event_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected $_categoryDataAdded = false;

    /**
    * Flag which marking that collection should not contain "Closed" events
    */ 
    protected $_skipClosed = false;

    /**
     * Intialize collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('enterprise_catalogevent/event');

    }

    /**
     * Redefining of standart field to filter adding, for aviability of
     * bit operations for display state
     *
     *
     * @param string|array $attribute
     * @param null|string|array $condition
     * @return Enterprise_CatalogEvent_Model_Mysql4_Event_Collection
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'display_state') {
            $field = $this->_getMappedField($field);
            if (is_array($condition) && isset($condition['eq'])) {
                $condition = $condition['eq'];
            }
            $this->_select->where('(' . $field . ' & ' . (int) $condition . ') = ' . (int) $condition);
            return $this;
        }
        parent::addFieldToFilter($field, $condition);
        return $this;
    }

    /**
     * Add filter for visible events on frontend
     *
     * @return Enterprise_CatalogEvent_Model_Mysql4_Event_Collection
     */
    public function addVisibilityFilter()
    {
        $this->_skipClosed = true;
        $this->addFieldToFilter('status', array(
            'nin' => Enterprise_CatalogEvent_Model_Event::STATUS_CLOSED
        ));
        return $this;
    }

    /**
     * Set sort order
     *
     * @param string $field
     * @param string $direction
     * @param boolean $unshift
     * @return Enterprise_CatalogEvent_Model_Mysql4_Event_Collection
     */
    protected function _setOrder($field, $direction, $unshift = false)
    {
        if ($field == 'category_name' && $this->_categoryDataAdded) {
            $field = 'category_position';
        }
        return parent::_setOrder($field, $direction, $unshift);
    }

    /**
     * Add category data to collection select (name, position)
     *
     * @return Enterprise_CatalogEvent_Model_Mysql4_Event_Collection
     */
    public function addCategoryData()
    {
        if (!$this->_categoryDataAdded) {
             $this->getSelect()
                ->joinLeft(array('category' => $this->getTable('catalog/category')), 'category.entity_id = main_table.category_id', array('category_position' => 'position'))
                ->joinLeft(array('category_name_attribute' => $this->getTable('eav/attribute')), 'category_name_attribute.entity_type_id = category.entity_type_id
                    AND category_name_attribute.attribute_code = \'name\'', array())
                ->joinLeft(array('category_varchar' => $this->getTable('catalog/category') . '_varchar'), 'category_varchar.entity_id = category.entity_id
                    AND category_varchar.attribute_id = category_name_attribute.attribute_id
                    AND category_varchar.store_id = 0
                ', array('category_name' => 'value'));
            $this->_map['fields']['category_name'] = 'category_varchar.value';
            $this->_map['fields']['category_position'] = 'category.position';
            $this->_categoryDataAdded = true;
        }
        return $this;
    }

    /**
     * Add sorting by status.
     * first will be open, then upcoming
     *
     * @return Enterprise_CatalogEvent_Model_Mysql4_Event_Collection
     */
    public function addSortByStatus()
    {
        $this->getSelect()
            ->order(array(
                $this->getConnection()->quoteInto(
                    'IF (main_table.status = ?, 0, 1) ASC',
                    Enterprise_CatalogEvent_Model_Event::STATUS_OPEN
                ),
                $this->getConnection()->quoteInto(
                    'IF (main_table.status = ?, main_table.date_end, main_table.date_start) ASC',
                    Enterprise_CatalogEvent_Model_Event::STATUS_OPEN
                ),
                'main_table.sort_order ASC'
        ));

        return $this;
    }

    /**
     * Add image data
     *
     * @return Enterprise_CatalogEvent_Model_Mysql4_Event_Collection
     */
    public function addImageData()
    {
        $this->getSelect()->joinLeft(
            array('event_image' => $this->getTable('event_image')),
            'event_image.event_id = main_table.event_id
            AND event_image.store_id = ' . Mage::app()->getStore()->getId() . '',
            array('image' => 'IFNULL(event_image.image, event_image_default.image)')
        )
        ->joinLeft(
            array('event_image_default' => $this->getTable('event_image')),
            'event_image_default.event_id = main_table.event_id
            AND event_image_default.store_id = 0',
            array())
        ->group('main_table.event_id');

        return $this;
    }

    /**
     * Limit collection by specified category paths
     *
     * @param array $allowedPaths
     * @return Enterprise_CatalogEvent_Model_Mysql4_Event_Collection
     */
    public function capByCategoryPaths($allowedPaths)
    {
        $this->addCategoryData();
        $paths = array();
        foreach ($allowedPaths as $path) {
            $paths[] = $this->getConnection()->quoteInto('category.path = ?', $path);
            $paths[] = $this->getConnection()->quoteInto('category.path LIKE ?', $path . '/%');
        }
        if ($paths) {
            $this->getSelect()->where(implode(' OR ', $paths));
        }
        return $this;
    }

    /**
     * Overriden _afterLoad() implementation
     *
     * @return  Varien_Data_Collection_Db
     */    
    protected function _afterLoad()
    {
        $events = parent::_afterLoad();
        foreach ($events->_items as $event) {
            $event->updateStatus();
            if ($this->_skipClosed && $event->getStatus() == Enterprise_CatalogEvent_Model_Event::STATUS_CLOSED) {
                $this->removeItemByKey($event->getId());
            }
        }
        return $this;
    }

    /**
     * Reset collection
     */    
    protected function _reset() 
    {
        parent::_reset();
        $this->_skipClosed = false;
        return $this;
    }

}
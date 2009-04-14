<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog Event resource model
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 */
class Enterprise_CatalogEvent_Model_Mysql4_Event extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Intialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('enterprise_catalogevent/event', 'event_id');
        $this->addUniqueField(array('field' => 'category_id' , 'title' => Mage::helper('enterprise_catalogevent')->__('Event for selected category')));
    }

    /**
     * Before model save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Enterprise_CatalogEvent_Model_Mysql4_Event
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (strlen($object->getSortOrder()) === 0) {
            $object->setSortOrder(null);
        }

        return parent::_beforeSave($object);
    }

    /**
     * Retreive category ids with events
     *
     * @param int|string|Mage_Core_Model_Store $storeId
     * @return array
     */
    public function getCategoryIdsWithEvent($storeId = null)
    {
        $rootCategoryId = Mage::app()->getStore($storeId)->getRootCategoryId();

        /* @var $select Varien_Db_Select */
        $select = Mage::getModel('catalog/category')->getCollection()
            ->setStoreId(Mage::app()->getStore($storeId)->getId())
            ->addIsActiveFilter()
            ->addPathsFilter(Mage_Catalog_Model_Category::TREE_ROOT_ID . '/' . $rootCategoryId)
            ->getSelect();

        $parts = $select->getPart(Zend_Db_Select::FROM);

        if (isset($parts['main_table'])) {
            $categoryCorrelationName = 'main_table';
        } else {
            $categoryCorrelationName = 'e';

        }

        $select->reset(Zend_Db_Select::COLUMNS);

        $select->columns(array('entity_id','level', 'path'), $categoryCorrelationName);


        $select
            ->joinLeft(
                array('event'=>$this->getMainTable()),
                'event.category_id = ' . $categoryCorrelationName . '.entity_id',
                'event_id'
        )->order($categoryCorrelationName . '.level ASC');

        $eventCategories = $this->_getReadAdapter()->fetchAssoc($select);

        if (empty($eventCategories)) {
            return array();
        }

        $result = array();

        foreach ($eventCategories as $categoryId => $category) {
            if ($category['event_id'] === null && isset($category['level']) && $category['level'] > 2) {
                foreach ($eventCategories as $parentId => $parentCategory) {
                    if (isset($category['path'])) {
                        if (strpos($category['path'], $parentCategory['path']) !== false &&
                            isset($result[$parentId]) &&
                            $result[$parentId] !== null) {
                            $result[$categoryId] = $result[$parentId];
                            break;
                        }
                    }
                }
                if (!isset($result[$categoryId])) {
                    $result[$categoryId] = null;
                }
            } else {
                $result[$categoryId] = $category['event_id'];
            }
        }

        return $result;
    }

    /**
     * After model save (save event image)
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Enterprise_CatalogEvent_Model_Mysql4_Event
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $this->_getWriteAdapter()->delete(
            $this->getTable('event_image'),
             $object->getIdFieldName() . ' = ' . $object->getId() .
            ' AND store_id = ' . $object->getStoreId()
        );

        if ($object->getImage() !== null) {
            $this->_getWriteAdapter()->insert(
                $this->getTable('event_image'),
                array(
                    $object->getIdFieldName() => $object->getId(),
                    'store_id' => $object->getStoreId(),
                    'image' => $object->getImage()
                )
            );
        }
        return parent::_afterSave($object);
    }

    /**
     * After model load (loads event image)
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Enterprise_CatalogEvent_Model_Mysql4_Event
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('event_image'), array(
                'type' => 'IF(store_id = 0, \'default\', \'store\')',
                'image'
            ))
            ->where($object->getIdFieldName() . ' = ?', $object->getId())
            ->where('store_id IN (0,?)', $object->getStoreId());

        $images = $this->_getReadAdapter()->fetchPairs($select);

        if (isset($images['store'])) {
            $object->setImage($images['store']);
            $object->setImageDefault(isset($images['default']) ? $images['default'] : '');
        }

        if (isset($images['default']) && !isset($images['store'])) {
            $object->setImage($images['default']);
        }

        return parent::_afterLoad($object);
    }
}

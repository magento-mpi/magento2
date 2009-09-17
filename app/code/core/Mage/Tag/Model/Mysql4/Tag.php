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
 * @category   Mage
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tag resourse model
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tag_Model_Mysql4_Tag extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('tag/tag', 'tag_id');
    }

    /**
     * Initialize unique fields
     *
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(array(
            'field' => 'name',
            'title' => Mage::helper('tag')->__('Tag')
        ));
        return $this;
    }

    /**
     * Loading tag by name
     *
     * @param Mage_Tag_Model_Tag $model
     * @param string $name
     * @return unknown
     */
    public function loadByName($model, $name)
    {
        if( $name ) {
            $read = $this->_getWriteAdapter();
            $select = $read->select();
            if (Mage::helper('core/string')->strlen($name) > 255) {
                $name = Mage::helper('core/string')->substr($name, 0, 255);
            }

            $select->from($this->getMainTable())
                ->where('name = ?', $name);
            $data = $read->fetchRow($select);

            $model->setData( ( is_array($data) ) ? $data : array() );
        } else {
            return false;
        }
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId() && $object->getStatus()==$object->getApprovedStatus()) {
            $searchTag = new Varien_Object();
            $this->loadByName($searchTag, $object->getName());
            if($searchTag->getData($this->getIdFieldName()) && $searchTag->getStatus()==$object->getPendingStatus()) {
                $object->setId($searchTag->getData($this->getIdFieldName()));
            }
        }

        if (Mage::helper('core/string')->strlen($object->getName()) > 255) {
            $object->setName(Mage::helper('core/string')->substr($object->getName(), 0, 255));
        }

        return parent::_beforeSave($object);
    }

    /**
     * Getting statistics, reading rows from summary table where tag_id = current  into buffer.
     * Replacing our buffer array with new statistics and incoming data.
     *
     * @param Mage_Tag_Model_Tag $object
     * @return Mage_Tag_Model_Tag
     */
    public function aggregate($object)
    {
        $tagId = (int)$object->getId();
        $storeId = (int)$object->getStoreId();

        /**
         * generating new statistics data for current tag
         */
        $selectLocal = $this->_getWriteAdapter()->select()
            ->from(
                array('main'  => $this->getTable('relation')),
                array(
                    'customers'=>'COUNT(DISTINCT main.customer_id)',
                    'products'=>'COUNT(DISTINCT main.product_id)',
                    'store_id',
                    'uses'=>'COUNT(main.tag_relation_id)'
                )
            )
            ->join(array('store' => $this->getTable('core/store')),
                'store.store_id=main.store_id AND store.store_id>0',
                array()
            )
            ->join(array('product_website' => $this->getTable('catalog/product_website')),
                'product_website.website_id=store.website_id AND product_website.product_id=main.product_id',
                array()
            )
            ->where('main.tag_id = ?', $tagId)
            ->where('main.active')
            ->where('main.customer_id IS NOT NULL')
            ->group('main.store_id');

        $selectGlobal = $this->_getWriteAdapter()->select()
            ->from(
                array('main'=>$this->getTable('relation')),
                array(
                    'customers'=>'COUNT(DISTINCT main.customer_id)',
                    'products'=>'COUNT(DISTINCT main.product_id)',
                    'store_id'=>'( 0 )' /* Workaround*/,
                    'uses'=>'COUNT(main.tag_relation_id)'
                )
            )
            ->join(array('store' => $this->getTable('core/store')),
                'store.store_id=main.store_id AND store.store_id>0',
                array()
            )
            ->join(array('product_website' => $this->getTable('catalog/product_website')),
                'product_website.website_id=store.website_id AND product_website.product_id=main.product_id',
                array()
            )
            ->where('main.tag_id = ?', $tagId)
            ->where('main.customer_id IS NOT NULL')
            ->where('main.active');

        $selectHistorical = $this->_getWriteAdapter()->select()
            ->from(
                array('main'=>$this->getTable('relation')),
                array('historical_uses'=>'COUNT(main.tag_relation_id)',
                'store_id')
            )
            ->join(array('store' => $this->getTable('core/store')),
                'store.store_id=main.store_id AND store.store_id>0',
                array()
            )
            ->join(array('product_website' => $this->getTable('catalog/product_website')),
                'product_website.website_id=store.website_id AND product_website.product_id=main.product_id',
                array()
            )
            ->group('main.store_id')
            ->where('main.customer_id IS NOT NULL')
            ->where('main.tag_id = ?', $tagId);

       $selectHistoricalGlobal = $this->_getWriteAdapter()->select()
            ->from(
                array('main'=>$this->getTable('relation')),
                array('historical_uses'=>'COUNT(main.tag_relation_id)')
            )
            ->join(array('store' => $this->getTable('core/store')),
                'store.store_id=main.store_id AND store.store_id>0',
                array()
            )
            ->join(array('product_website' => $this->getTable('catalog/product_website')),
                'product_website.website_id=store.website_id AND product_website.product_id=main.product_id',
                array()
            )
            ->where('main.tag_id = ?', $tagId)
            ->where('main.customer_id IS NOT NULL');

        /**
         * getting all summary rows for current tag
         */
        $selectSummary = $this->_getWriteAdapter()->select()
            ->from(
                array('main' => $this->getTable('summary')),
                array('store_id', 'base_popularity')
            )
            ->where('main.tag_id = ?', $tagId)
            ->where('main.store_id != ?', 0);

        $summaryAll = $this->_getWriteAdapter()->fetchAssoc($selectSummary);

        $historicalAll = $this->_getWriteAdapter()->fetchAll($selectHistorical);

        $historicalCache = array();

        foreach ($historicalAll as $historical) {
            $historicalCache[$historical['store_id']] = $historical['historical_uses'];
        }

        $summaries = $this->_getWriteAdapter()->fetchAll($selectLocal);

        /**
         * replacing old data with new one
         */
        if ($row = $this->_getWriteAdapter()->fetchRow($selectGlobal)) {
            $historical = $this->_getWriteAdapter()->fetchOne($selectHistoricalGlobal);

            if($historical) {
                $row['historical_uses'] = $historical;
            }

            $summaries[] = $row;
        }

        if ($object->hasBasePopularity() && $storeId ) {
            $summaryAll[$storeId]['store_id'] = $storeId;
            $summaryAll[$storeId]['base_popularity'] = $object->getBasePopularity();
        }

        foreach ($summaries as $row) {
            $storeId = (int)$row['store_id'];
            foreach ($row as $key => $value) {
                $summaryAll[$storeId][$key] = $value;
            }
        }

        foreach ($summaryAll as $key => $row) {
            $summaryAll[$key]['tag_id'] = $tagId;
        }

        /**
         * removing old data from table for current tag
         */
        $this->_getWriteAdapter()->delete($this->getTable('summary'), $this->_getWriteAdapter()->quoteInto('tag_id = ?', $tagId));

        /**
         * inserting new data in table for current tag
         */
        foreach ($summaryAll as $summary) {
            if(!isset($summary['historical_uses'])) {
                $summary['historical_uses'] = isset($historicalCache[$summary['store_id']]) ? $historicalCache[$summary['store_id']] : 0;
            }

            $summary['tag_id'] = $tagId;
            $summary['popularity'] = $summary['historical_uses'];

            if (isset($summary['uses']) && is_null($summary['uses'])) {
                $summary['uses'] = 0;
            }

            $this->_getWriteAdapter()->insert($this->getTable('summary'), $summary);
        }

        return $object;
    }

    /**
     * Add summary data
     *
     * @param Mage_Tag_Model_Tag $object
     * @return Mage_Tag_Model_Tag
     */
    public function addSummary($object)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from($this->getTable('summary'))
            ->where('tag_id = ?', (int)$object->getId())
            ->where('store_id = ?', (int)$object->getStoreId())
            ->limit(1);

        $row = $this->_getWriteAdapter()->fetchRow($select);
        if ($row) {
            $object->addData($row);
        }
        return $object;
    }

    /**
     * Fetch store ids in which tag visible
     *
     * @param Mage_Tag_Model_Mysql4_Tag $object
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from($this->getTable('tag/summary'), array('store_id'))
            ->where('tag_id = ?', $object->getId());
        $storeIds = $this->_getWriteAdapter()->fetchCol($select);

        $object->setVisibleInStoreIds($storeIds);

        return $this;
    }
}

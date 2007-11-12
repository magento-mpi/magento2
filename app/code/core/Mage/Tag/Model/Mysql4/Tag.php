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
 * @category   Mage
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tag resourse model
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Tag_Model_Mysql4_Tag extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('tag/tag', 'tag_id');
        $this->_uniqueFields = array( array('field' => 'name', 'title' => __('Tag') ) );
    }

    public function loadByName($model, $name)
    {
        if( $name ) {
            $read = $this->getConnection('read');
            $select = $read->select();

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

        if (iconv_strlen($object->getName(), 'UTF-8') > 255) {
            $object->setName(iconv_substr($object->getName(), 0, 255));
        }

        return parent::_beforeSave($object);
    }

    public function aggregate($object)
    {
        $selectLocal = $this->getConnection('read')->select()
            ->from(array('main'=>$this->getTable('relation')), array('customers'=>'COUNT(DISTINCT main.customer_id)','products'=>'COUNT(DISTINCT main.product_id)','store_id', 'uses'=>'COUNT(main.tag_relation_id)'))
            ->join(array('store'=>$this->getTable('catalog/product_store')), 'store.product_id = main.product_id AND store.store_id=main.store_id', array())
            ->group('main.store_id')
            ->where('main.tag_id = ?', $object->getId())
            ->where('main.active');

        $selectGlobal = $this->getConnection('read')->select()
            ->from(array('main'=>$this->getTable('relation')), array('customers'=>'COUNT(DISTINCT main.customer_id)','products'=>'COUNT(DISTINCT main.product_id)','store_id'=>'( 0 )' /* Workaround*/, 'uses'=>'COUNT(main.tag_relation_id)'))
            ->join(array('store'=>$this->getTable('catalog/product_store')), 'store.product_id = main.product_id AND store.store_id=main.store_id', array())
            ->where('main.tag_id = ?', $object->getId())
            ->where('main.active');

        $selectHistorical = $this->getConnection('read')->select()
            ->from(array('main'=>$this->getTable('relation')), array('historical_uses'=>'COUNT(main.tag_relation_id)', 'store_id'))
            ->join(array('store'=>$this->getTable('catalog/product_store')), 'store.product_id = main.product_id AND store.store_id=main.store_id', array())
            ->group('main.store_id')
            ->where('main.tag_id = ?', $object->getId());

       $selectHistoricalGlobal = $this->getConnection('read')->select()
            ->from(array('main'=>$this->getTable('relation')), array('historical_uses'=>'COUNT(main.tag_relation_id)'))
            ->join(array('store'=>$this->getTable('catalog/product_store')), 'store.product_id = main.product_id AND store.store_id=main.store_id', array())
            ->where('main.tag_id = ?', $object->getId());

        $historicalAll = $this->getConnection('read')->fetchAll($selectHistorical);
        $historicalCache = array();
        foreach ($historicalAll as $historical) {
            $historicalCache[$historical['store_id']] = $historical['historical_uses'];
        }

        $summaries = $this->getConnection('read')->fetchAll($selectLocal);
        if ($row = $this->getConnection('read')->fetchRow($selectGlobal)) {
            $historical = $this->getConnection('read')->fetchOne($selectHistoricalGlobal);

            if($historical) {
                $row['historical_uses'] = $historical;
            }

            $summaries[] = $row;
        }

        $this->getConnection('write')->delete($this->getTable('summary'), $this->getConnection('write')->quoteInto('tag_id = ?', $object->getId()));

        foreach ($summaries as $summary) {
            if(!isset($summary['historical_uses'])) {
                $summary['historical_uses'] = isset($historicalCache[$summary['store_id']]) ? $historicalCache[$summary['store_id']] : 0;
            }
            $summary['tag_id'] = $object->getId();
            $summary['popularity'] = $summary['historical_uses'];
            if (is_null($summary['uses'])) {
                $summary['uses'] = 0;
            }

            $this->getConnection('write')->insert($this->getTable('summary'), $summary);
        }

        return $object;
    }

    public function addSummary($object)
    {
        $select = $this->getConnection('read')->select()
            ->from($this->getTable('summary'))
            ->where('tag_id = ?', $object->getId())
            ->where('store_id = ?', $object->getStoreId());

        $row = $this->getConnection('read')->fetchAll($select);

        $object->addData($row);
        return $object;
    }
}
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

    public function aggregate($object)
    {
        $selectLocal = $this->getConnection('read')->select()
            ->from(array('main'=>$this->getTable('relation')), array('historical_uses'=>'COUNT(main.tag_relation_id)', 'customers'=>'COUNT(DISTINCT main.customer_id)','products'=>'COUNT(DISTINCT main.product_id)','store_id', 'uses'=>'SUM(main.active)'))
            ->join(array('store'=>$this->getTable('catalog/product_store')), 'store.product_id = main.product_id AND store.store_id=main.store_id', array())
            ->group('main.store_id')
            ->where('main.tag_id = ?', $object->getId());

        $selectGlobal = $this->getConnection('read')->select()
            ->from(array('main'=>$this->getTable('relation')), array('historical_uses'=>'COUNT(main.tag_relation_id)', 'customers'=>'COUNT(DISTINCT main.customer_id)','products'=>'COUNT(DISTINCT main.product_id)','store_id'=>'( 0 )' /* Workaround*/, 'uses'=>'SUM(main.active)'))
            ->join(array('store'=>$this->getTable('catalog/product_store')), 'store.product_id = main.product_id AND store.store_id=main.store_id', array())
            ->group('main.tag_id')
            ->where('main.tag_id = ?', $object->getId());



        $summaries = $this->getConnection('read')->fetchAll($selectLocal);
        if ($row = $this->getConnection('read')->fetchRow($selectGlobal)) {
            $summaries[] = $row;
        }

        $this->getConnection('write')->delete($this->getTable('summary'), $this->getConnection('write')->quoteInto('tag_id = ?', $object->getId()));

        foreach ($summaries as $summary) {
            $summary['tag_id'] = $object->getId();
            $summary['popularity'] = $summary['historical_uses'];
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
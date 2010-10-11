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
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Collection Advanced 
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogSearch_Model_Resource_Advanced_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{
    /**
     * Add not indexable fields to search
     *
     * @param array $fields
     * @return Mage_CatalogSearch_Model_Resource_Advanced_Collection
     */
    public function addFieldsToFilter($fields)
    {
        if ($fields) {
            $previousSelect = null;
            foreach ($fields as $table => $conditions) {
                foreach ($conditions as $attributeId => $conditionValue) {
                    $select = $this->getConnection()->select();
                    $select->from(array('t1' => $table), 'entity_id');
                    $conditionData = array();
                    if (is_array($conditionValue)) {
                        if (isset($conditionValue['in'])){
                            $conditionData[] = array('in' => $conditionValue['in']);
                        }
                        elseif (isset($conditionValue['in_set'])) {
                            //TODO: use LIKE as
                            //foreach ($conditionValue['in_set'])) {
                            //    $conditionDataOr[] = array('like' => $conditionValue['in_set'][0]);
                            //    $conditionDataOr[] = array('like' => $conditionValue['in_set'][0] . ',%');
                            //    $conditionDataOr[] = array('like' => '%,' . $conditionValue['in_set'][0]);
                            //    $conditionDataOr[] = array('like' => '%,' . $conditionValue['in_set'][0] . ',%');
                            //}

                            $conditionData[] = array('regexp' => '\'(^|,)('.implode('|', $conditionValue['in_set']).')(,|$)\'');
                        }
                        elseif (isset($conditionValue['like'])) {
                            $conditionData[] = array ('like' => $conditionValue['like']);
                        }
                        elseif (isset($conditionValue['from']) && isset($conditionValue['to'])) {
                            if ($conditionValue['from']) {
                                if (!is_numeric($conditionValue['from'])){
                                    $conditionValue['from'] = Mage::getSingleton('core/date')->gmtDate(null, $conditionValue['from']);
                                }
                                $conditionData[] = array('gteq' => $conditionValue['from']);
                            }
                            if ($conditionValue['to']) {
                                if (!is_numeric($conditionValue['to'])){
                                    $conditionValue['to'] = Mage::getSingleton('core/date')->gmtDate(null, $conditionValue['to']);
                                }
                                $conditionData[] = array('lteq' => $conditionValue['to']);
                            }
                        }
                    } else {
                        $conditionData[] = array('= ?', $conditionValue);
                    }
                    if (!is_numeric($attributeId)) {
                        foreach ($conditionData as $data) {
                            $select->where($this->getConnection()->prepareSqlCondition('t1.'.$attributeId, $data));
                        }
                    }
                    else {
                        $storeId = $this->getStoreId();
                        $select->joinLeft(
                            array('t2' => $table),
                            $this->getConnection()->quoteInto('t1.entity_id = t2.entity_id AND t1.attribute_id = t2.attribute_id AND t2.store_id=?', $storeId),
                            array()
                        );
                        $select->where('t1.store_id = ?', 0);
                        $select->where('t1.attribute_id = ?', $attributeId);

                        $ifCondition = $this->getConnection()->getCheckSql('t2.value_id>0', 't2.value', 't1.value');
                        foreach ($conditionData as $data) {
                            $select->where($this->getConnection()->prepareSqlCondition($ifCondition, $data));
                        }
                    }

                    if (!is_null($previousSelect)) {
                        $select->where('t1.entity_id IN (?)', new Zend_Db_Expr($previousSelect));
                    }
                    $previousSelect = $select;
                }
            }
            $this->addFieldToFilter('entity_id', array('in' => new Zend_Db_Expr($select)));
        }

        return $this;
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
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
            $conn = $this->getConnection();
            foreach ($fields as $table => $conditions) {
                foreach ($conditions as $attributeId => $conditionValue) {
                    $select = $conn->select();
                    $select->from(array('t1' => $table), 'entity_id');
                    $conditionData = array();

                    if (!is_numeric($attributeId)) {
                        $field = 't1.'.$attributeId;
                    }
                    else {
                        $storeId = $this->getStoreId();
                        $onCondition = 't1.entity_id = t2.entity_id'
                                . ' AND t1.attribute_id = t2.attribute_id'
                                . ' AND t2.store_id=?';

                        $select->joinLeft(
                            array('t2' => $table),
                            $conn->quoteInto($onCondition, $storeId),
                            array()
                        );
                        $select->where('t1.store_id = ?', 0);
                        $select->where('t1.attribute_id = ?', $attributeId);

                        if (array_key_exists('price_index', $this->getSelect()->getPart(Magento_DB_Select::FROM))) {
                            $select->where('t1.entity_id = price_index.entity_id');
                        }

                        $field = $this->getConnection()->getCheckSql('t2.value_id>0', 't2.value', 't1.value');

                    }

                    if (is_array($conditionValue)) {
                        if (isset($conditionValue['in'])){
                            $conditionData[] = array('in' => $conditionValue['in']);
                        }
                        elseif (isset($conditionValue['in_set'])) {
                            $conditionParts = array();
                            foreach ($conditionValue['in_set'] as $value) {
                                $conditionParts[] = array('finset' => $value);
                            }
                            $conditionData[] = $conditionParts;
                        }
                        elseif (isset($conditionValue['like'])) {
                            $conditionData[] = array ('like' => $conditionValue['like']);
                        }
                        elseif (isset($conditionValue['from']) && isset($conditionValue['to'])) {
                            $invalidDateMessage = Mage::helper('Mage_CatalogSearch_Helper_Data')->__('Please specify correct data.');
                            if ($conditionValue['from']) {
                                if (!Zend_Date::isDate($conditionValue['from'])) {
                                    Mage::throwException($invalidDateMessage);
                                }
                                if (!is_numeric($conditionValue['from'])){
                                    $conditionValue['from'] = Mage::getSingleton('Magento_Core_Model_Date')
                                        ->gmtDate(null, $conditionValue['from']);
                                    if (!$conditionValue['from']) {
                                        $conditionValue['from'] = Mage::getSingleton('Magento_Core_Model_Date')->gmtDate();
                                    }
                                }
                                $conditionData[] = array('gteq' => $conditionValue['from']);
                            }
                            if ($conditionValue['to']) {
                                if (!Zend_Date::isDate($conditionValue['to'])) {
                                    Mage::throwException($invalidDateMessage);
                                }
                                if (!is_numeric($conditionValue['to'])){
                                    $conditionValue['to'] = Mage::getSingleton('Magento_Core_Model_Date')
                                        ->gmtDate(null, $conditionValue['to']);
                                    if (!$conditionValue['to']) {
                                        $conditionValue['to'] = Mage::getSingleton('Magento_Core_Model_Date')->gmtDate();
                                    }
                                }
                                $conditionData[] = array('lteq' => $conditionValue['to']);
                            }

                        }
                    } else {
                        $conditionData[] = array('eq' => $conditionValue);
                    }


                    foreach ($conditionData as $data) {
                        $select->where($conn->prepareSqlCondition($field, $data));
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

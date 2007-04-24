<?php

class Mage_Sales_Model_Mysql4_Document_Collection extends Varien_Data_Collection_Db
{
    protected $_docType;
    protected $_attributeTypes;
    protected $_documentTable;
    protected $_attributeTable;
    protected $_retrieveAttributes = array();
    protected $_filterIds = array();
    protected $_filterAttributes = array();
    protected $_sqlSelectStr = '';
    
    public function __construct() 
    {
        parent::__construct(Mage::registry('resources')->getConnection('sales_read'));
    }
    
    public function setDocType($docType)
    {
        $this->_docType = $docType;
        $this->_attributeTypes = Mage::getConfig()->getGlobalCollection('salesAttributes')->$docType;
        $this->_documentTable = Mage::registry('resources')->getTableName('sales_resource', $docType);
        $this->_idField = $docType.'_id';
        $this->_attributeTable = Mage::registry('resources')->getTableName('sales_resource', $docType.'_attribute');
        
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('sales', $docType));
    }
    
    public function addEntitiesToSelect(array $entities)
    {
        foreach ($entities as $entityType=>$attributes) {
            foreach ($attributes as $attributeCode=>$attributeName) {
                $this->addAttributeToSelect($entityType, $attributeCode, $attributeName);
            }
        }
        return $this;
    }
    
    /**
     * Add an entity attribute to select
     * 
     * if no $attributeCode is specified will retrieve all attributes for this $entityType
     *
     * @param string $entityType
     * @param string $attributeCode
     * @return Mage_Sales_Model_Mysql4_Document_Collection
     */
    public function addAttributeToSelect($entityType, $attributeCode=null)
    {
        $attributeType = $this->_getAttributeType($entityType, $attributeCode);
        if (false===$attributeType) {
            return $this;
        }
        if (is_null($attributeCode)) {
            $this->_retrieveAttributes[$attributeType][$entityType] = array();
        } else {
            $this->_retrieveAttributes[$attributeType][$entityType][] = $attributeCode;
        }
        return $this;
    }
    
    /**
     * Add an entity attribute to collection filter
     * 
     * Condition will be used by self::_getConditionSql method
     *
     * @param string $entityType
     * @param string $attributeCode
     * @param integer|string|array $value
     * @return Mage_Sales_Model_Mysql4_Document_Collection
     */
    public function addAttributeToFilter($entityType, $attributeCode, $attributeCondition=null)
    {
        $attributeType = $this->_getAttributeType($entityType, $attributeCode);
        if (false===$attributeType) {
            return $this;
        }
        $this->_filterAttributes[$attributeType][$entityType][$attributeCode] = $attributeCondition;
        return $this;
    }
    
    /**
     * Add a document id condition to collection filter
     * 
     * Condition will be used by self::_getConditionSql method
     *
     * @param integer|array $value
     * @return Mage_Sales_Model_Mysql4_Document_Collection
     */
    public function addIdToFilter($value)
    {
        $this->_filterIds[] = $value;
        return $this;
    }
        
    protected function _getAttributeType($entityType, $attributeCode)
    {
        $attributeConfig = $this->_attributeTypes->$entityType->$attributeCode;
        if (isset($attributeConfig)) {
            return (string)$attributeConfig->type;
        }
        return false;
    }
    
    /**
     * Build SQL statement for condition
     * 
     * If $condition integer or string - exact value will be filtered
     * 
     * If $condition is array is - one of the following structures is expected:
     * - array("from"=>$fromValue, "to"=>$toValue)
     * - array("like"=>$likeValue)
     * - array("neq"=>$notEqualValue)
     * - array("in"=>array($inValues))
     * - array("nin"=>array($notInValues))
     * 
     * If non matched - sequential array is expected and OR conditions 
     * will be built using above mentioned structure
     *
     * @param string $fieldName
     * @param integer|string|array $condition
     * @return string
     */
    protected function _getConditionSql($fieldName, $condition) {
        $sql = '';
        if (is_array($condition)) {
            if (!empty($condition['from']) && !empty($condition['to'])) {
                $sql = $this->_conn->quoteInto("$fieldName between ?", $condition['from']);
                $sql = $this->_conn->quoteInto("$sql and ?", $condition['to']);
            } elseif (!empty($condition['neq'])) {
                $sql = $this->_conn->quoteInto("$fieldName != ?", $condition['neq']);
            } elseif (!empty($condition['like'])) {
                $sql = $this->_conn->quoteInto("$fieldName like ?", $condition['like']);
            } elseif (!empty($condition['in'])) {
                $sql = $this->_conn->quoteInto("$fieldName in (?)", $condition['in']);
            } elseif (!empty($condition['nin'])) {
                $sql = $this->_conn->quoteInto("$fieldName not in (?)", $condition['nin']);
            } else {
                $orSql = array();
                foreach ($condition as $orCondition) {
                    $orSql[] = "(".$this->_getConditionSql($fieldName, $orCondition).")";
                }
                $sql = "(".join(" or ", $orSql).")";
            }
        } else {
            $sql = $this->_conn->quoteInto("$fieldName = ?", $condition);
        }
        return $sql;
    }
    
    /**
     * Build the filters part of sql statement
     *
     * @return Mage_Sales_Model_Mysql4_Document_Collection
     */
    protected function _renderFilters()
    {
        $sqlUnionArr = array();
                
        if (!empty($this->_retrieveAttributes)) {
            foreach ($this->_retrieveAttributes as $attributeType=>$entities) {
                $sqlUnionArr[$attributeType]['select'] = 'select * from '.$this->_attributeTable.'_'.$attributeType;
    
                $entityWhereArr = array();
                foreach ($entities as $entityType=>$attributes) {
                    $where = $this->_conn->quoteInto("$attributeTable.entity_type=?", $entityType);
                    if (!empty($attributes)) {
                        $where .= $this->_conn->quoteInto("and $attributeTable.attribute_code in (?)", $attributes);
                    }
                    $entityWhereArr[] = "(".$where.")";
                }
                if (!empty($entityWhereArr)) {
                    $sqlUnionArr[$attributeType]['where'][] = "(".join(" or ", $entityWhereArr).")";
                }
            }
        }

        if (!empty($this->_filterAttributes)) {
            foreach ($this->_filterAttributes as $attributeType=>$entities) {
                if (empty($sqlUnionArr[$attributeTable])) {
                    $sqlUnionArr[$attributeType]['select'] = 'select * from '.$this->_attributeTable.'_'.$attributeType;
                }
                
                foreach ($entities as $entityType=>$attributes) {
                    $where = $this->_conn->quoteInto("$attributeTable.entity_type=?", $entityType);
                    foreach ($attributes as $attributeCode=>$attributeCondition) {
                        $where .= " and ".$this->_getConditionSql("$attributeTable.attribute_value", $attributeCondition);
                    }
                    $sqlUnionArr[$attributeType]['where'][] = "(".$where.")";
                }
            }
        }
        
        if (!empty($this->_filterIds)) {
            foreach ($this->_filterIds as $idCondition) {
                $sqlIdsArr[] = "(".$this->_getConditionSql("$this->_documentTable.$this->_idField", $idCondition).")";
            }
            $sqlWhereIds = "(".join(" and ", $sqlIdsArr).")";
            foreach ($sqlUnionArr as $sqlSelectArr) {
                $sqlSelectArr['where'][] = $sqlWhereIds;
            }
        }
        
        if (!empty($sqlUnionArr)) {
            $sqlUnionStrArr = array();
            foreach ($sqlUnionArr as $sqlSelectArr) {
                $sqlSelect = $sqlSelectArr['select'];
                if (!empty($sqlSelectArr['where'])) {
                    $sqlSelect .= ' where '.join(" and ", $sqlSelectArr['where']);
                }
                $sqlUnionStrArr[] = $sqlSelect;
            }
            $this->_sqlSelectStr = join(" union ", $sqlUnionStrArr);
        }
        
        return $this;
    }
    
    protected function _renderOrders()
    {
        $this->_sqlSelectStr .= " order by ".join(', ', $this->_orders);
        
        return $this;
    }
    
    protected function _renderLimit()
    {
        if ($this->_curPage<1) {
            $this->_curPage=1;
        }
        
        if ($this->_pageSize) {
            $this->_curPage = ($this->_curPage > 0) ? $this->_curPage : 1;
            $this->_pageSize = ($this->_pageSize > 0) ? $this->_pageSize : 1;
            $this->_sqlSelectStr .= " limit ".(int)$this->_pageSize;
            $this->_sqlSelectStr .= " offset ".((int)$this->_pageSize*($this->_curPage-1));
        }
        
        return $this;
    }
    
    /**
     * Load data
     *
     * @return  Mage_Sales_Model_Mysql4_Document_Collection
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        $this->_renderFilters()
             ->_renderOrders()
             ->_renderLimit();

        if($printQuery) {
            echo $this->_sqlSelectStr;
        }
        
        if($logQuery){
            Mage::log($this->_sqlSelectStr);
        }

        $docs = array();
        $data = $this->_conn->fetchAll($this->_sqlSelectStr);
        if (is_array($data)) {
            foreach ($data as $attr) {
                $docs[$attr[$this->_idField]][$attr['entity_id']]['type'] = $attr['entity_type'];
                $docs[$attr[$this->_idField]][$attr['entity_id']]['data'][$attr['attribute_code']] = $attr['attribute_value'];
            }
            foreach ($docs as $docId=>$entities) {
                $docObj = Mage::getModel('sales', $this->_docType)->setDocumentId($docId);
                foreach ($entities as $entityId=>$entityData) {
                    if ('self'===$entity['type']) {
                        $docObj->addData($entityData);
                    } else {
                        $entity = Mage::getModel('sales', $this->_docType.'_entity_'.$entity['type'])
                            ->setEntityId($entityId)
                            ->addData($entityData);
                        $docObj->addEntity($entity);
                    }
                }
                $this->addItem($docObj);
            }
        }
        return $this;
    }

}
<?php

class Mage_Sales_Model_Mysql4_Document_Collection extends Varien_Data_Collection_Db
{
    protected $_docType;
    protected $_attributeTypes;
    protected $_documentTable;
    protected $_attributeTable;
    protected $_selectAttributes = array();
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
        
        $this->_sqlSelect->from($this->_documentTable);
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
     * if no $attributeCode is specified will select all attributes for this $entityType
     *
     * @param string $entityType
     * @param string $attributeCode
     * @return Mage_Sales_Model_Mysql4_Document_Collection
     */
    public function addAttributeToSelect($entityType, $attributeCode=null)
    {
        if (is_null($attributeCode)) {
            foreach (array('datetime', 'decimal', 'int', 'text', 'varchar') as $attributeType) {
                $this->_selectAttributes[$attributeType][$entityType] = array();
            }
        } else {
            $attributeType = $this->_getAttributeType($entityType, $attributeCode);
            if (false===$attributeType) {
                return $this;
            }
            $this->_selectAttributes[$attributeType][$entityType][] = $attributeCode;
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
        $attributeTable = $this->_attributeTable.'_'.$attributeType;
        $attributeTableAlias = $entityType.'_'.$attributeCode;
        $attributeAlias = ($entityType!=='self' ? $entityType.'_' : '').$attributeCode;
        $joinCondition = "$attributeTableAlias.$this->_idField=$this->_documentTable.$this->_idField"
            ." and ".$this->_conn->quoteInto("$attributeTableAlias.entity_type=?", $entityType)
            ." and ".$this->_conn->quoteInto("$attributeTableAlias.attribute_code=?", $attributeCode)
            ." and ".$this->_getConditionSql("$attributeTableAlias.attribute_value", $attributeCondition);
        $this->_sqlSelect->join("$attributeTable as $attributeTableAlias", $joinCondition, array("$attributeTableAlias.attribute_value"=>$attributeAlias));
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
    public function addIdToFilter($condition)
    {
        $this->_sqlSelect->where($this->_getConditionSql("$this->_documentTable.$this->_idField", $condition));
        return $this;
    }    
    
    /**
     * Load data
     *
     * @return  Mage_Sales_Model_Mysql4_Document_Collection
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        parent::loadData($printQuery = false, $logQuery = false);
        
        $this->_loadAttributes();

        return $this;
    }
        
    protected function _getAttributeType($entityType, $attributeCode)
    {
        return (string)$this->_attributeTypes->descend("$entityType/$attributeCode/type");
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
     * Build sql to select attributes of filtered documents
     *
     * @param array $ids document ids to retrieve
     * @return string
     */
    protected function _getAttributesSql(array $ids)
    {
        $sqlUnionArr = array();
                
        if (empty($this->_selectAttributes) || empty($ids)) {
            return false;
        }
        
        $idsSql = $this->_conn->quoteInto("$this->_idField in (?)", $ids);
        
        foreach ($this->_selectAttributes as $attributeType=>$entities) {
            $attributeTable = $this->_attributeTable.'_'.$attributeType;
            if (empty($sqlUnionArr[$attributeType])) {
                $sqlUnionArr[$attributeType]['select'] = "select * from $attributeTable";
                $sqlUnionArr[$attributeType]['where'][] = $idsSql;
            }

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

        $sqlUnionStrArr = array();
        foreach ($sqlUnionArr as $sqlSelectArr) {
            $sqlSelect = $sqlSelectArr['select'];
            if (!empty($sqlSelectArr['where'])) {
                $sqlSelect .= ' where '.join(" and ", $sqlSelectArr['where']);
            }
            $sqlUnionStrArr[] = $sqlSelect;
        }
        $sql = join(" union ", $sqlUnionStrArr);
        
        return $sql;
    }
    
    protected function _loadAttributes()
    {
        if (!$this->getSize()) {
            return false;
        }
        
        $ids = array();
        foreach ($this->getItems() as $doc) {
            $ids[] = $doc->getDocumentId();
        }
        
        $attributesSql = $this->_getAttributesSql($ids);
        if (empty($attributesSql)) {
            return false;
        }
        
        $attributes = $this->_conn->fetchAll($attributesSql);
        if (!is_array($attributes) || empty($attributes)) {
            return false;
        }
        
        $docs = array();
        foreach ($attributes as $attr) {
            $docs[$attr[$this->_idField]][$attr['entity_id']]['type'] = $attr['entity_type'];
            $docs[$attr[$this->_idField]][$attr['entity_id']]['data'][$attr['attribute_code']] = $attr['attribute_value'];
        }
        foreach ($this->getItems() as $docObj) {
            $docId = $docObj->getDocumentId();
            if (!is_array($docs[$docId]) || empty($docs[$docId])) {
                continue;
            }
            foreach ($docs[$docId] as $entityId=>$entityArr) {
                if ('self'===$entityArr['type']) {
                    $docObj->addData($entityArr['data']);
                } else {
                    $entity = Mage::getModel('sales', $this->_docType.'_entity_'.$entityArr['type'])
                        ->setEntityId($entityId)
                        ->addData($entityArr['data']);
                    $docObj->addEntity($entity);
                }
            }
        }
        return true;
    }
}
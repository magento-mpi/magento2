<?php

class Mage_Sales_Model_Mysql4_Document_Collection extends Varien_Data_Collection_Db
{
    protected $_docType;
    protected $_attributeTypes;
    protected $_documentTable;
    protected $_attributeTable;
    protected $_selectAttributes = array();
    protected $_filterAttributes = array();
    protected $_sqlSelectStr = '';
    
    public function __construct() 
    {
        parent::__construct(Mage::registry('resources')->getConnection('sales_read'));
    }
    
    public function setDocType($docType)
    {
        $this->_docType = $docType;
        $this->_attributeTypes = Mage::getConfig()->getNode('global/salesAttributes/'.$docType);
        $this->_documentTable = Mage::registry('resources')->getTableName('sales_resource', $docType);
        $this->_idField = $docType.'_id';
        $this->_attributeTable = Mage::registry('resources')->getTableName('sales_resource', $docType.'_attribute');
        
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('sales', $docType));
        
        $this->_sqlSelect->from($this->_documentTable);
    }
    
    public function addEntitiesSelect(array $entities)
    {
        foreach ($entities as $entityType=>$attributes) {
            foreach ($attributes as $attributeCode=>$attributeName) {
                $this->addAttributeSelect($entityType.'/'.$attributeCode, $attributeName);
            }
        }
        return $this;
    }
    
    /**
     * Add an entity attribute to select
     * 
     * if no $attributeCode is specified will select all attributes for this $entityType
     *
     * @param string $entityType entityType[/attributeCode]
     * @param string $attributeCode
     * @return Mage_Sales_Model_Mysql4_Document_Collection
     */
    public function addAttributeSelect($entityAttribute)
    {
        $arr = explode('/', $entityAttribute);
        if (empty($arr[1])) {
            $arr[1] = null;
        }
        list($entityType, $attributeCode) = $arr;

        if (empty($attributeCode)) {
            foreach (array('datetime', 'decimal', 'int', 'text', 'varchar') as $attributeType) {
                $this->_selectAttributes[$attributeType][$entityType] = array();
            }
        } else {
            $attributeType = $this->_getAttributeType($entityAttribute);
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
     * @param string $entityType entityType/attributeCode
     * @param string $attributeCode
     * @param integer|string|array $value
     * @return Mage_Sales_Model_Mysql4_Document_Collection
     */
    public function addAttributeFilter($entityAttribute, $attributeCondition=null)
    {
        if (!empty($this->_filterAttrubutes[$entityAttribute])) {
            return $this; //todo: make adding conditions to existing attribute filter
        }
        $arr = explode('/', $entityAttribute);
        if (empty($arr[1])) {
            throw new Exception("$entityAttribute: Attribute name should be entityType/attributeCode.");
            return $this;
        }
        list($entityType, $attributeCode) = $arr;

        $attributeType = $this->_getAttributeType($entityAttribute);
        if (false===$attributeType) {
            return $this;
        }
        $attributeTable = $this->_attributeTable.'_'.$attributeType;
        $attributeAlias = $this->_getAttributeAlias($entityAttribute);
        $attributeTableAlias = $attributeAlias;#$entityType.'_'.$attributeCode;
        $joinCondition = "$attributeTableAlias.$this->_idField=$this->_documentTable.$this->_idField"
            ." and ".$this->_conn->quoteInto("$attributeTableAlias.entity_type=?", $entityType)
            ." and ".$this->_conn->quoteInto("$attributeTableAlias.attribute_code=?", $attributeCode);
        if (!empty($attributeCondition)) {
            $joinCondition .= " and ".$this->_getConditionSql("$attributeTableAlias.attribute_value", $attributeCondition);
        }
        $this->_sqlSelect->join("$attributeTable as $attributeTableAlias", $joinCondition, array($attributeAlias=>"$attributeAlias.attribute_value"));
        $this->_filterAttrubutes[$entityAttribute] = true;
        return $this;
    }
    
    /**
     * Add a document id condition to collection filter
     *
     * @param integer|array $value
     * @return Mage_Sales_Model_Mysql4_Document_Collection
     */
    public function addIdFilter($condition)
    {
        $this->_sqlSelect->where($this->_getConditionSql("$this->_documentTable.$this->_idField", $condition));
        return $this;
    }    
    
    /**
     * Set sort order for the collection
     *
     * @param string $field entityType/attributeCode
     * @param string $direction
     * @return Mage_Sales_Model_Mysql4_Document_Collection
     */
    public function setOrder($entityAttribute, $direction = 'desc')
    {
        $this->addAttributeFilter($entityAttribute);
        return parent::setOrder($this->_getAttributeAlias($entityAttribute), $direction);
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
    
    protected function _getAttributeAlias($entityAttribute)
    {
        $arr = explode('/', $entityAttribute);
        if (empty($arr[1])) {
            throw new Exception("$entityAttribute: Attribute name should be entityType/attributeCode.");
            return $this;
        }
        list($entityType, $attributeCode) = $arr;   
        #return ($entityType!=='self' ? $entityType.'_' : '').$attributeCode;
        return $entityType.'_'.$attributeCode;
    }
        
    protected function _getAttributeType($entityAttribute)
    {
        return (string)$this->_attributeTypes->descend("$entityAttribute/type");
    }
    
    /**
     * Build sql to select attributes of filtered documents
     *
     * @param array $ids document ids to retrieve
     * @return string
     */
    protected function _getAttributesSql($globalWhere)
    {
        $sqlUnionArr = array();
                
        if (empty($this->_selectAttributes)) {
            return false;
        }
        
        foreach ($this->_selectAttributes as $attributeType=>$entities) {
            $attributeTable = $this->_attributeTable.'_'.$attributeType;
            if (empty($sqlUnionArr[$attributeType])) {
                $sqlUnionArr[$attributeType]['select'] = "select * from $attributeTable";
                $sqlUnionArr[$attributeType]['where'][] = $globalWhere;
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
        
        $idsSql = $this->_conn->quoteInto("$this->_idField in (?)", $this->getColumnValues($this->_idField));
        $attributesSql = $this->_getAttributesSql($idsSql);        
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
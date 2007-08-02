<?php

class Mage_Catalog_Model_Mysql4_Product_Link_Collection extends Varien_Data_Collection_Db 
{
    protected $_linkTable;
    protected $_productCollection;
    
    public function __construct() 
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('catalog_read'));
        
        $this->_linkTable = Mage::getSingleton('core/resource')->getTableName('catalog/product_link');
        
        $this->_productCollection = Mage::getResourceModel('catalog/product_collection');
        
        $this->_sqlSelect->from($this->_linkTable);
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('catalog/product_link'));
    }
    
    public function getProductCollection()
    {
        return $this->_productCollection;
    }
    
    public function addProductFilter($condition)
    {
        $this->_sqlSelect->where($this->_getConditionSql("$this->_linkTable.product_id", $condition));
        return $this;
    }
    
    public function addTypeFilter($condition)
    {
        $this->_sqlSelect->where($this->_getConditionSql("$this->_linkTable.link_type_id", $condition));
        return $this;
    }
    
    /**
     * Build sql to select attributes of found links
     *
     * @return boolean
     */
    protected function _loadAttributes()
    {
        $sqlUnionStrArr = array();
        
        $attrTable = Mage::getSingleton('core/resource')->getTableName('catalog/product_link_attribute');
        $arrLinkId = $this->getColumnValues('link_id');
        if (empty($arrLinkId)) {
            return false;
        }

        $linkIdsWhere = $this->_getConditionSql("`$attrTable`.`link_id`", array('in'=>$arrLinkId));
        
        foreach (array('decimal', 'varchar') as $attributeType) {
            $attrValueTable = Mage::getSingleton('core/resource')->getTableName('catalog/product_link_attribute_'.$attributeType);
            $sqlUnionStrArr[$attributeType] = "select `$attrTable`.`link_id`, `$attrTable`.`product_link_attribute_code` as code, `$attrValueTable`.`value`"
                ." from $attrValueTable"
                ." inner join $attrTable on $attrTable.product_link_attribute_id=$attrValueTable.product_link_attribute_id"
                ." where $linkIdsWhere";
        }

        $attributes = $this->_conn->fetchAll(join(" union ", $sqlUnionStrArr));
        if (empty($attributes) || !is_array($attributes)) {
            return false;
        }
        
        $linkAttributes = array();
        foreach ($attributes as $attr) {
            $linkAttributes[$attr['link_id']][$attr['code']] = $attr['value'];
        }
        
        foreach ($this->getItems() as $link) {
            $linkId = $link->getLinkId();
            if (!empty($linkAttributes[$linkId]) && is_array($linkAttributes[$linkId])) {
                $link->addData($linkAttributes[$linkId]);
            }
        }
        return true;
    }
    
    protected function _loadLinkedProducts()
    {
        $arrProductId = $this->getColumnValues('linked_product_id');
        if (empty($arrProductId)) {
            return false;
        }
        
        $this->getProductCollection()->addProductFilter(array('in'=>$arrProductId));
        $linkedProducts = $this->getProductCollection()->loadData();
            
        foreach ($this->getItems() as $item) {
            $item->setProduct($linkedProducts->getItemById($item->getLinkedProductId()));
        }
        return true;
    }
    
    public function loadData($printQuery=false, $logQuery=false)
    {
        if (!parent::loadData($printQuery, $logQuery)) {
            return $this;
        }
        
        //$this->_loadAttributes();
        $this->_loadLinkedProducts();        

        return $this;
    }
}